<?php

namespace Vnecoms\VendorsPdf\Model;


class Order extends \Vnecoms\PdfPro\Model\Order
{
    public function initVendorOrderData(\Vnecoms\VendorsSales\Model\Order $vendorOrder){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $vendorOrder->getOrder();
        $this->setTranslationByStoreId($order->getStoreId());
        $orderCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        
        $sourceData = $this->process($vendorOrder->getData(), $orderCurrencyCode, $baseCurrencyCode);
        $sourceData['increment_id'] = $order->getIncrementId();
        $sourceData['customer'] = $this->getCustomerData($om->create('\Magento\Customer\Model\Customer')->load($order->getCustomerId()));
        $sourceData['created_at_formated'] = $this->getFormatedDate($vendorOrder->getCreatedAt());
        $sourceData['updated_at_formated'] = $this->getFormatedDate($vendorOrder->getUpdatedAt());
        $sourceData['giftmessage'] = $this->giftHelper->initMessage($order);
        $sourceData['billing'] = $this->getAddressData($order->getBillingAddress());
        $sourceData['customer_dob'] = isset($sourceData['customer_dob']) ? $this->getFormatedDate($sourceData['customer_dob']) : '';
        
        /*if order is not virtual */
        if (!$order->getIsVirtual()) {
            $sourceData['shipping'] = $this->getAddressData($order->getShippingAddress());
        }
        /*Get Payment Info */
        $paymentInfo = $this->_helperPayment->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea(\Magento\Framework\App\Area::AREA_ADMINHTML)
            ->toPdf();

        $paymentInfo = str_replace('{{pdf_row_separator}}', '<br>', $paymentInfo);
        $sourceData['payment'] =
            array('code' => $order->getPayment()->getMethodInstance()->getCode(),
                'name' => $order->getPayment()->getMethodInstance()->getTitle(),
                'info' => $paymentInfo,
            );
        $sourceData['payment_info'] = $paymentInfo;
        $sourceData['totals'] = $this->getTotalData($vendorOrder);
        $sourceData['items'] = $this->getItemsData($vendorOrder);
        $sourceData['is_vendor_order'] = true;
        $sourceData['vendor'] = $vendorOrder->getVendor();
        
        $apiKey = $om->get('Vnecoms\VendorsPdf\Helper\Data')->getApiKey(
            $vendorOrder->getVendorId(),
            $order->getStoreId(), 
            $order->getCustomerGroupId()
        );
        
        $sourceData = new \Magento\Framework\DataObject($sourceData);
        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after',
            ['source' => $sourceData, 'model' => $vendorOrder, 'type' => 'vendor_order', 'order' => $this]);
        
        $orderData = new \Magento\Framework\DataObject(['key' => $apiKey, 'data' => $sourceData]);
        $this->revertTranslation();
        
        return $orderData;
    }
    
    /**
     * Get totals data
     * 
     * @param \Vnecoms\VendorsSales\Model\Order $source
     * @return \Magento\Framework\DataObject
     */
    public function getTotalData(\Vnecoms\VendorsSales\Model\Order $source){
        $totals = $this->_getTotalsList();
        $totalArr = array();
        foreach ($totals as $total) {
            $total->setOrder($source)
            ->setSource($source);
            if ($total->canDisplay()) {
                $area = $total->getSourceField() == 'grand_total' ? 'footer' : 'body';
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $totalArr[$area][] = new \Magento\Framework\DataObject(array('label' => __($totalData['label']),
                        'value' => $totalData['amount'], ));
                }
            }
        }
        
        return new \Magento\Framework\DataObject($totalArr);
    }
    /**
     * Get items data
     * 
     * @param \Vnecoms\VendorsSales\Model\Order $source
     * @return multitype:\Magento\Framework\DataObject
     */
    public function getItemsData(\Vnecoms\VendorsSales\Model\Order $source){
        $order = $source->getOrder();
        $orderCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        
        $result = [];
        foreach ($source->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $itemModel = $this->orderItemFactory->create(['item' => $item]);
            if ($item->getProductType() == 'bundle') {
                $itemData = array('is_bundle' => 1, 'name' => $item->getName(), 'sku' => $item->getSku());
                if ($itemModel->canShowPriceInfo($item)) {
                    $itemData['price'] = $this->helper->currency($item->getPrice(), $orderCurrencyCode);
                    $itemData['qty'] = $item->getQtyOrdered() * 1;
                    $itemData['tax'] = $this->helper->currency($item->getTaxAmount(), $orderCurrencyCode);
                    $itemData['subtotal'] = $this->helper->currency($item->getRowTotal(), $orderCurrencyCode);
                    $itemData['row_total'] = $this->helper->currency($item->getRowTotalInclTax(), $orderCurrencyCode);
                }
                $items = $itemModel->getChilds($item);
                $itemData['sub_items'] = array();
        
                foreach ($items as $_item) {
                    $bundleItem = array();
                    $attributes = $itemModel->getSelectionAttributes($_item);
                    if (!$attributes['option_label']) {
                        continue;
                    }
                    $bundleItem['label'] = $attributes['option_label'];
                    /*Product name */
                    if ($_item->getParentItem()) {
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
                        $bundleItem['qty'] = $_item->getQtyOrdered() * 1;
                        $bundleItem['tax'] = $this->helper->currency($_item->getTaxAmount(), $orderCurrencyCode);
                        $bundleItem['subtotal'] = $this->helper->currency($_item->getRowTotal(), $orderCurrencyCode);
                        $bundleItem['row_total'] = $this->helper->currency($_item->getRowTotalInclTax(),
                            $orderCurrencyCode);
                    }
                    $bundleItem = new \Magento\Framework\DataObject($bundleItem);
        
                    $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after',
                        ['source' => $bundleItem, 'model' => $_item, 'type' => 'item']);
                    $itemData['sub_items'][] = $bundleItem;
                }
            } else {
                $itemData = array(
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'price' => $this->helper->currency($item->getPrice(), $orderCurrencyCode),
                    'qty' => $item->getQtyOrdered() * 1,
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
                            $printValue = isset($option['print_value'])
                            ? $option['print_value'] : strip_tags($option['value']);
                            $optionData['value'] = $printValue;
                        }
                        $itemData['options'][] = new \Magento\Framework\DataObject($optionData);
                    }
                }
            }
            //var_dump($itemData);die();
        
            $itemData = new \Magento\Framework\DataObject($itemData);
        
            $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after',
                ['source' => $itemData, 'model' => $item, 'type' => 'item']);
            $result[] = $itemData;
        }
        
        return $result;
    }
}
