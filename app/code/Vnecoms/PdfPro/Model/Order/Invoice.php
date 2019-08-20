<?php

namespace Vnecoms\PdfPro\Model\Order;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class Invoice.
 *
 * @author VnEcoms team <vnecoms.com>
 */
class Invoice extends \Vnecoms\PdfPro\Model\AbstractPdf
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
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Vnecoms\PdfPro\Model\Order\Invoice\ItemFactory
     */
    protected $invoiceItemFactory;
    
    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param ListsInterface $listsInterface
     * @param ManagerInterface $event
     * @param \Magento\Framework\Locale\Resolver $locale
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Vnecoms\PdfPro\Helper\Giftmessage $giftmessage
     * @param \Magento\Payment\Helper\Data $helperPayment
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $option
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatterInterface
     * @param \Magento\Sales\Model\Order\Pdf\Config $config
     * @param \Magento\Framework\Logger\Monolog $logger
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \Vnecoms\PdfPro\Model\Order\Invoice\Item $invoiceItemFactory
     * @param array $data
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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\App\Emulation $emulation,
        \Vnecoms\PdfPro\Model\Order\Invoice\ItemFactory $invoiceItemFactory,
        array $data = []
    ) {
        $this->giftHelper = $giftmessage;
        $this->_helperPayment = $helperPayment;
        $this->pdfConfig = $config;
        $this->logger = $logger;
        $this->customerFactory = $customerFactory;
        $this->invoiceItemFactory = $invoiceItemFactory;
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
     * get base price attributes.
     *
     * @return array
     */
    public function getBasePriceAttributes()
    {
        return array(
            'base_grand_total',
            'base_tax_amount',
            'base_shipping_tax_amount',
            'base_discount_amount',
            'base_subtotal_incl_tax',
            'base_shipping_amount',
            'base_subtotal',
            'base_hidden_tax_amount',
            'base_shipping_hidden_tax_amnt',
            'base_shipping_incl_tax',
            'base_total_refunded',
            'base_cod_fee',
        );
    }

    /**
     * get price attributes.
     *
     * @return array
     */
    public function getPriceAttributes()
    {
        return array(
            'shipping_tax_amount',
            'tax_amount',
            'grand_total',
            'shipping_amount',
            'subtotal_incl_tax',
            'subtotal',
            'discount_amount',
            'hidden_tax_amount',
            'shipping_hidden_tax_amount',
            'shipping_incl_tax',
            'cod_fee',
        );
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

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return string
     *
     * @throws \Exception
     */
    public function initInvoiceData(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();

        $orderCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $this->setTranslationByStoreId($invoice->getStoreId());

        /*
         * init invoice data
         */
        $invoiceData = $this->process($invoice->getData(), $orderCurrencyCode, $baseCurrencyCode);
        /*init order data*/
        $orderData = \Magento\Framework\App\ObjectManager::getInstance()->create('\Vnecoms\PdfPro\Model\Order')->initOrderData($order);

        $invoiceData['order'] = ($orderData);
        $invoiceData['customer'] = $this->getCustomerData(\Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Customer\Model\Customer')->load($order->getCustomerId()));
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
        $invoiceData['totals'] = array();
        $invoiceData['items'] = array();

        /*
          * Get Items information
         */
        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }

            /**
             * @var \Vnecoms\PdfPro\Model\Order\Invoice\Item $itemModel
             */
            $itemModel = $this->invoiceItemFactory->create(['data' => ['item' => $item]]);
            if ($item->getOrderItem()->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
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
            $invoiceData['items'][] = $itemData;
        }

        /*
         * Get Totals information.
        */
        $totals = $this->_getTotalsList();
        $totalArr = array();
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($invoice);
            if ($total->canDisplay()) {
                $area = $total->getSourceField() == 'grand_total' ? 'footer' : 'body';
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $totalArr[$area][] = new \Magento\Framework\DataObject(array('label' => $totalData['label'], 'value' => $totalData['amount']));
                }
            }
        }
        $invoiceData['totals'] = new \Magento\Framework\DataObject($totalArr);
        $apiKey = $this->helper->getApiKey($order->getStoreId(), $order->getCustomerGroupId());

        $invoiceData = new \Magento\Framework\DataObject($invoiceData);

        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', array('source' => $invoiceData, 'model' => $invoice, 'type' => 'invoice'));

        $invoiceData = new \Magento\Framework\DataObject(array('key' => $apiKey, 'data' => $invoiceData));

        $this->revertTranslation();

        return $invoiceData;
        //return serialize($invoiceData);
    }
}
