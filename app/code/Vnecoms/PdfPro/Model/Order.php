<?php
/**
 * Copyright © 2017 Vnecoms. All rights reserved.
 */

namespace Vnecoms\PdfPro\Model;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class Order.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Order extends AbstractPdf
{
    /**
     * @var string
     */
    protected $_defaultTotalModel = 'Magento\Sales\Model\Order\Pdf\Total\DefaultTotal';

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_helperPayment;

    /**
     * @var \VnEcoms\PdfPro\Helper\Giftmessage
     */
    protected $giftHelper;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Config
     */
    protected $pdfConfig;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * @var \Vnecoms\PdfPro\Model\Order\ItemFactory
     */
    protected $orderItemFactory;

    /**
     * Order constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                $localeDate
     * @param \VnEcoms\PdfPro\Helper\Data                                         $helper
     * @param ListsInterface                                                      $listsInterface
     * @param ManagerInterface                                                    $event
     * @param \Magento\Framework\Locale\Resolver                                  $locale
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManagerInterface
     * @param \VnEcoms\PdfPro\Helper\Giftmessage                                  $giftmessage
     * @param \Magento\Payment\Helper\Data                                        $helperPayment
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $option
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface       $dateTimeFormatterInterface
     * @param \Magento\Sales\Model\Order\Pdf\Config                               $config
     * @param \Magento\Framework\Logger\Monolog                                   $logger
     * @param \Magento\Store\Model\App\Emulation                                  $emulation
     * @param \Vnecoms\PdfPro\Model\Order\ItemFactory                             $orderItemFactory
     * @param array                                                               $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Vnecoms\PdfPro\Helper\Data $helper,
        ListsInterface $listsInterface,
        ManagerInterface $event,
        \Magento\Framework\Locale\Resolver $locale,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Vnecoms\PdfPro\Helper\Giftmessage $giftmessage,
        \Magento\Payment\Helper\Data $helperPayment,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $option,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatterInterface,
        \Magento\Sales\Model\Order\Pdf\Config $config,
        \Magento\Framework\Logger\Monolog $logger,
        \Magento\Store\Model\App\Emulation $emulation,
        \Vnecoms\PdfPro\Model\Order\ItemFactory $orderItemFactory,
        array $data = []
    ) {
        $this->giftHelper = $giftmessage;
        $this->_helperPayment = $helperPayment;
        $this->pdfConfig = $config;
        $this->logger = $logger;
        $this->orderItemFactory = $orderItemFactory;
        parent::__construct($localeDate, $helper, $listsInterface, $event, $locale, $storeManagerInterface, $option, $dateTimeFormatterInterface, $emulation,$data);
    }

    /**
     * Sort totals list.
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    protected function _sortTotalsList($a, $b)
    {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }

        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
    }

    /**
     * @return array
     */
    public function getBasePriceAttributes()
    {
        return array(
            'base_discount_amount',
            'base_discount_canceled',
            'base_discount_invoiced',
            'base_discount_refunded',
            'base_grand_total',
            'base_shipping_amount',
            'base_shipping_canceled',
            'base_shipping_invoiced',
            'base_shipping_refunded',
            'base_shipping_tax_amount',
            'base_shipping_tax_refunded',
            'base_subtotal',
            'base_subtotal_canceled',
            'base_subtotal_invoiced',
            'base_subtotal_refunded',
            'base_tax_amount',
            'base_tax_canceled',
            'base_tax_invoiced',
            'base_tax_refunded',
            'base_to_global_rate',
            'base_to_order_rate',
            'base_to_order_rate',
            'base_total_canceled',
            'base_total_invoiced',
            'base_total_invoiced_cost',
            'base_total_offline_refunded',
            'base_total_online_refunded',
            'base_total_paid',
            'base_total_refunded',
            'base_adjustment_negative',
            'base_adjustment_positive',
            'base_shipping_discount_amount',
            'base_subtotal_incl_tax',
            'base_total_due',
            'base_shipping_hidden_tax_amnt',
            'base_hidden_tax_invoiced',
            'base_hidden_tax_refunded',
            'base_shipping_incl_tax',
            'base_shipping_hidden_tax_amount',
            'base_cod_fee',
        );
    }

    /**
     * @return array
     */
    public function getPriceAttributes()
    {
        return array(
            'discount_amount',
            'discount_canceled',
            'discount_invoiced',
            'discount_refunded',
            'grand_total',
            'shipping_amount',
            'shipping_canceled',
            'shipping_invoiced',
            'shipping_refunded',
            'shipping_tax_amount',
            'shipping_tax_refunded',
            'store_to_base_rate',
            'subtotal',
            'subtotal_canceled',
            'subtotal_invoiced',
            'subtotal_refunded',
            'tax_amount',
            'tax_canceled',
            'tax_invoiced',
            'tax_refunded',
            'total_canceled',
            'total_invoiced',
            'total_offline_refunded',
            'total_online_refunded',
            'total_paid',
            'total_refunded',
            'adjustment_negative',
            'adjustment_positive',
            'payment_authorization_amount',
            'shipping_discount_amount',
            'subtotal_incl_tax',
            'total_due',
            'hidden_tax_amount',
            'shipping_hidden_tax_amount',
            'hidden_tax_invoiced',
            'hidden_tax_refunded',
            'shipping_incl_tax',
            'cod_fee',
        );
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $source
     *
     * @return string
     *
     * @throws \Exception
     */
    public function initOrderData(\Magento\Sales\Api\Data\OrderInterface $source)
    {
        /**
         * @var \Magento\Sales\Model\Order $order
         */
        $order = $source;
        $source->setOrder(clone $order);
        $this->setTranslationByStoreId($order->getStoreId());
        $orderCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();

        $sourceData = $this->process($source->getData(), $orderCurrencyCode, $baseCurrencyCode);

        $sourceData['customer'] = $this->getCustomerData(\Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Customer\Model\Customer')->load($order->getCustomerId()));
        $sourceData['created_at_formated'] = $this->getFormatedDate($source->getCreatedAt());
        $sourceData['updated_at_formated'] = $this->getFormatedDate($source->getUpdatedAt());

      //  var_dump($sourceData['invoice']);
      //  var_dump($sourceData['customer']);die();
        //$sourceData['invoice'] = $this->getInvoiceData($order);
       /*Init gift message*/
        $sourceData['giftmessage'] = $this->giftHelper->initMessage($order);

        $sourceData['billing'] = $this->getAddressData($source->getBillingAddress());
       // var_dump($sourceData['billing']);die();
        $sourceData['customer_dob'] = isset($sourceData['customer_dob']) ? $this->getFormatedDate($sourceData['customer_dob']) : '';

        /*if order is not virtual */
        if (!$source->getIsVirtual()) {
            $sourceData['shipping'] = $this->getAddressData($source->getShippingAddress());
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
        $sourceData['totals'] = array();
        $sourceData['items'] = array();

        /*
    	 * Get Items information
    	*/
        foreach ($source->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            /**
             * @var \Vnecoms\PdfPro\Model\Order\Item $itemModel
             */
            $itemModel = $this->orderItemFactory->create(['data' => ['item' => $item]]);
            if ($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
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

            $itemData = new \Magento\Framework\DataObject($itemData);

            $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after',
                ['source' => $itemData, 'model' => $item, 'type' => 'item']);
            $sourceData['items'][] = $itemData;
        }

        /*
    	 * Get Totals information.
    	*/
        $totals = $this->_getTotalsList();
        $totalArr = array();
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);
            if ($total->canDisplay()) {
                $area = $total->getSourceField() == 'grand_total' ? 'footer' : 'body';
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $totalArr[$area][] = new \Magento\Framework\DataObject(array('label' => __($totalData['label']),
                        'value' => $totalData['amount'], ));
                }
            }
        }
        $sourceData['totals'] = new \Magento\Framework\DataObject($totalArr);

        $apiKey = $this->helper->getApiKey($order->getStoreId(), $order->getCustomerGroupId());//var_dump($apiKey);
        //echo "<pre>";var_dump($sourceData);die();

        //check if order has invoice
        if($order->hasInvoices()) {
            $sourceData['invoice'] = new \Magento\Framework\DataObject(['increment_id' => $order->getInvoiceCollection()->getFirstItem()->getData('increment_id')]);
        }
        $sourceData = new \Magento\Framework\DataObject($sourceData);

        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after',
            ['source' => $sourceData, 'model' => $order, 'type' => 'order', 'order' => $this]);
        $orderData = new \Magento\Framework\DataObject(['key' => $apiKey, 'data' => $sourceData]);
        $this->revertTranslation();
      //  die();

       // var_dump($order->getInvoiceCollection()->getFirstItem()->getData('increment_id'));die();
        return $orderData;
        //echo "<pre>";var_dump($orderData);die();
        //return serialize($orderData);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    protected function _getTotalsList()
    {
        $totals = $this->pdfConfig->getTotals();
        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = [];
        foreach ($totals as $index => $totalInfo) {
            $class = isset($totalInfo['model'])?$totalInfo['model']:$this->_defaultTotalModel;
            $totalModel = \Magento\Framework\App\ObjectManager::getInstance()->create($class);
            if ($totalModel instanceof \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal) {
                $totalInfo['model'] = $totalModel;
            } else {
                //throw exception
                throw new \Exception(
                    __('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                );
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }
        return $totalModels;
    }

    public function getInvoiceData(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $invoices = $order->getInvoiceCollection()->getFirstItem();
        return $invoices->getData();
    }
}
