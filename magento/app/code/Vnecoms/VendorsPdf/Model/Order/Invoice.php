<?php

namespace Vnecoms\VendorsPdf\Model\Order;

use Magento\Framework\App\ObjectManager;

class Invoice extends \Vnecoms\PdfPro\Model\Order\Invoice
{
    /**
     * @param \Vnecoms\VendorsSales\Model\Order\Invoice $invoice
     *
     * @return string
     *
     * @throws \Exception
     */
    public function initVendorInvoiceData(\Vnecoms\VendorsSales\Model\Order\Invoice $vendorInvoice)
    {
        $om = ObjectManager::getInstance();
        $invoice = $vendorInvoice->getInvoice();
        $order = $invoice->getOrder();

        $orderCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $this->setTranslationByStoreId($invoice->getStoreId());

        /*
         * init invoice data
         */
        $invoiceData = $this->process($vendorInvoice->getData(), $orderCurrencyCode, $baseCurrencyCode);
        /*init order data*/
        $orderData = $om->create('\Vnecoms\VendorsPdf\Model\Order')->initVendorOrderData($vendorInvoice->getOrder());
        $invoiceData['increment_id'] = $invoice->getIncrementId();
        $invoiceData['order'] = ($orderData);
        $invoiceData['customer'] = $this->getCustomerData($om->create('\Magento\Customer\Model\Customer')->load($order->getCustomerId()));
        $invoiceData['created_at_formated'] = $this->getFormatedDate($invoice->getCreatedAt());
        $invoiceData['updated_at_formated'] = $this->getFormatedDate($invoice->getUpdatedAt());
        $invoiceData['billing'] = $this->getAddressData($invoice->getBillingAddress());

        /*if order is not virtual */
        if (!$order->getIsVirtual()) {
            $invoiceData['shipping'] = $this->getAddressData($invoice->getShippingAddress());
        }

        /*Get Payment Info */
        $paymentInfo = $this->_helperPayment->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea(\Magento\Framework\App\Area::AREA_ADMINHTML)
            ->toPdf();

        $paymentInfo = str_replace('{{pdf_row_separator}}', '<br />', $paymentInfo);
        $paymentInfo = htmlspecialchars_decode($paymentInfo);
        $invoiceData['payment'] = array('code' => $order->getPayment()->getMethodInstance()->getCode(),
            'name' => $order->getPayment()->getMethodInstance()->getTitle(),
            'info' => $paymentInfo,
        );
        $invoiceData['payment_info'] = $paymentInfo;
        $invoiceData['shipping_description'] = $order->getShippingDescription();
        
        $invoiceData['totals'] = $this->getTotalData($vendorInvoice);
        $invoiceData['items'] = $this->getItemsData($vendorInvoice);
       
        
        $apiKey = $om->get('Vnecoms\VendorsPdf\Helper\Data')->getApiKey(
            $vendorInvoice->getVendorId(),
            $order->getStoreId(), 
            $order->getCustomerGroupId()
        );

        $invoiceData = new \Magento\Framework\DataObject($invoiceData);

        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', array('source' => $invoiceData, 'model' => $vendorInvoice, 'type' => 'vendor_invoice'));

        $invoiceData = new \Magento\Framework\DataObject(array('key' => $apiKey, 'data' => $invoiceData));

        $this->revertTranslation();

        return $invoiceData;
    }
    
    /**
     * Get totals data
     * 
     * @param \Vnecoms\VendorsSales\Model\Order $source
     * @return \Magento\Framework\DataObject
     */
    public function getTotalData(\Vnecoms\VendorsSales\Model\Order\Invoice $vendorInvoice){
        $totals = $this->_getTotalsList();
        $totalArr = array();
        foreach ($totals as $total) {
            $total->setOrder($vendorInvoice->getOrder())
            ->setSource($vendorInvoice);
            if ($total->canDisplay()) {
                $area = $total->getSourceField() == 'grand_total' ? 'footer' : 'body';
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $totalArr[$area][] = new \Magento\Framework\DataObject(array('label' => $totalData['label'], 'value' => $totalData['amount']));
                }
            }
        }
        return new \Magento\Framework\DataObject($totalArr);
    }
    
    /**
     * Get items data
     * 
     * @param \Vnecoms\VendorsSales\Model\Order\Invoice $vendorInvoice
     * @return multitype:\Magento\Framework\DataObject
     */
    public function getItemsData(\Vnecoms\VendorsSales\Model\Order\Invoice $vendorInvoice){
        $invoice = $vendorInvoice->getInvoice();
        $order = $invoice->getOrder();
        $orderCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode(); 
               
        $result = [];        
        foreach ($vendorInvoice->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            $itemModel = new \Vnecoms\PdfPro\Model\Order\Invoice\Item(['item' => $item]);
        
            if ($item->getOrderItem()->getProductType() == 'bundle') {
                $itemData = array('is_bundle' => 1, 'name' => $item->getName(), 'sku' => $item->getSku());
                if ($itemModel->canShowPriceInfo($item)) {
                    $itemData['price'] = $this->helper->currency($item->getPrice(), $orderCurrencyCode);
                    $itemData['qty'] = $item->getQty() * 1;
                    $itemData['tax'] = $this->helper->currency($item->getTaxAmount(), $orderCurrencyCode);
                    $itemData['subtotal'] = $this->helper->currency($item->getRowTotal(), $orderCurrencyCode);
                    $itemData['row_total'] = $this->helper->currency($item->getRowTotalInclTax(), $orderCurrencyCode);
                }
                $itemData['sub_items'] = array();
                $items = $itemModel->getChilds($item);
                foreach ($items as $_item) {
                    $bundleItem = array();
                    $attributes = $itemModel->getSelectionAttributes($_item);
                    // draw SKUs
                    if (!$_item->getOrderItem()->getParentItem()) {
                        continue;
                    }
                    $bundleItem['label'] = $attributes['option_label'];
                    /*Product name */
                    if ($_item->getOrderItem()->getParentItem()) {
                        $name = $itemModel->getValueHtml($_item);
                    } else {
                        $name = $_item->getName();
                    }
                    $bundleItem['value'] = $name;
                    $bundleItem['sku'] = $_item->getSku();
                    /* price */
                    if ($itemModel->canShowPriceInfo($_item)) {
                        $price = $order->formatPriceTxt($_item->getPrice());
                        $bundleItem['price'] = $this->helper->currency($_item->getPrice(), $orderCurrencyCode);
                        $bundleItem['qty'] = $_item->getQty() * 1;
                        $bundleItem['tax'] = $this->helper->currency($_item->getTaxAmount(), $orderCurrencyCode);
                        $bundleItem['subtotal'] = $this->helper->currency($_item->getRowTotal(), $orderCurrencyCode);
                        $bundleItem['row_total'] = $this->helper->currency($_item->getRowTotalInclTax(), $orderCurrencyCode);
                    }
                    $bundleItem = new \Magento\Framework\DataObject($bundleItem);
                    $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', array('source' => $bundleItem, 'model' => $_item, 'type' => 'item'));
                    $itemData['sub_items'][] = $bundleItem;
                }
            } else {
                $itemData = array(
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'price' => $this->helper->currency($item->getPrice(), $orderCurrencyCode),
                    'qty' => $item->getQty() * 1,
                    'tax' => $this->helper->currency($item->getTaxAmount(), $orderCurrencyCode),
                    'subtotal' => $this->helper->currency($item->getRowTotal(), $orderCurrencyCode),
                    'row_total' => $this->helper->currency($item->getRowTotalInclTax(), $orderCurrencyCode),
                );
                $options = $itemModel->getItemOptions($item);
                $itemData['options'] = array();
                if ($options) {
                    foreach ($options as $option) {
                        $optionData = array();
                        $optionData['label'] = strip_tags($option['label']);
        
                        if ($option['value']) {
                            $printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                            $optionData['value'] = $printValue;
                        }
                        $itemData['options'][] = new \Magento\Framework\DataObject($optionData);
                    }
                }
            }
            $itemData = new \Magento\Framework\DataObject($itemData);
            $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', array('source' => $itemData, 'model' => $item, 'type' => 'item'));
            $result[] = $itemData;
        }
        
        return $result;
    }
}
