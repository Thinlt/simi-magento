<?php

namespace Vnecoms\PdfPro\Model\Order;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class Creditmemo.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Creditmemo extends \Vnecoms\PdfPro\Model\AbstractPdf
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
     * @var \Vnecoms\PdfPro\Model\Order\Creditmemo\ItemFactory
     */
    protected $creditmemoItemFactory;
    
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
     * @param \Vnecoms\PdfPro\Model\Order\Creditmemo\ItemFactory $creditmemoItemFactory
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
        \Vnecoms\PdfPro\Model\Order\Creditmemo\ItemFactory $creditmemoItemFactory,
        array $data = []
    ) {
        $this->giftHelper = $giftmessage;
        $this->_helperPayment = $helperPayment;
        $this->pdfConfig = $config;
        $this->logger = $logger;
        $this->customerFactory = $customerFactory;
        $this->creditmemoItemFactory = $creditmemoItemFactory;
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

    public function getBasePriceAttributes()
    {
        return array(
            'base_shipping_tax_amount',
            'base_discount_amount',
            'base_adjustment_negative',
            'base_subtotal_incl_tax',
            'base_shipping_amount',
            'base_adjustment',
            'base_subtotal',
            'base_grand_total',
            'base_adjustment_positive',
            'base_tax_amount',
            'base_hidden_tax_amount',
            'base_shipping_incl_tax',
            'base_cod_fee',
        );
    }
    /*Get all price attribute */
    public function getPriceAttributes()
    {
        return array(
            'adjustment_positive',
            'grand_total',
            'shipping_amount',
            'subtotal_incl_tax',
            'adjustment_negative',
            'discount_amount',
            'subtotal',
            'adjustment',
            'shipping_tax_amount',
            'tax_amount',
            'hidden_tax_amount',
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
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     */
    public function initCreditmemoData(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        /*
         * @var \Magento\Sales\Model\Order
         */
        $order = $creditmemo->getOrder();
        $this->setTranslationByStoreId($creditmemo->getStoreId());

        $orderCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();

        $creditmemoData = $this->process($creditmemo->getData(), $orderCurrencyCode, $baseCurrencyCode);

        $orderData = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Vnecoms\PdfPro\Model\Order')->initOrderData($order);
        $creditmemoData['order'] = ($orderData);

        /*
         * @var \Magento\Customer\Model\Customer
         */
        $customer = $this->customerFactory->create()->load($order->getCustomerId());

        //var_dump($customer->getData());die();
        $creditmemoData['customer'] = $this->getCustomerData($customer);
        //die();
        $creditmemoData['created_at_formated'] = $this->getFormatedDate($creditmemo->getCreatedAt());
        $creditmemoData['updated_at_formated'] = $this->getFormatedDate($creditmemo->getUpdatedAt());

        $creditmemoData['billing'] = $this->getAddressData($creditmemo->getBillingAddress());
        /*if order is not virtual */
        if (!$order->getIsVirtual()) {
            $creditmemoData['shipping'] = $this->getAddressData($creditmemo->getShippingAddress());
        }

       // die();
        /*Get Payment Info */
        $paymentInfo = $this->_helperPayment->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea(\Magento\Framework\App\Area::AREA_ADMINHTML)
            ->toPdf();

        //echo $paymentInfo;die();

        $paymentInfo = str_replace('{{pdf_row_separator}}', ' <br/>', $paymentInfo);//echo $paymentInfo;die();
        $creditmemoData['payment'] =
            array('code' => $order->getPayment()->getMethodInstance()->getCode(),
                'name' => $order->getPayment()->getMethodInstance()->getTitle(),
                'info' => $paymentInfo,
            );
        $creditmemoData['payment_info'] = $paymentInfo;
        $creditmemoData['shipping_description'] = $order->getShippingDescription();

        $creditmemoData['items'] = array();
        $orderCurrencyCode = $order->getOrderCurrencyCode();

        /*
    	 * Get Items information
    	*/
        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }

            /**
             * @var \Vnecoms\PdfPro\Model\Order\Creditmemo\Item $itemModel
             */
            $itemModel = $this->creditmemoItemFactory->create(['data' => ['item' => $item]]);
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
            $creditmemoData['items'][] = $itemData;
        }

        /*
    	 * Get Totals information.
    	*/
        $totals = $this->_getTotalsList($creditmemo);
        $totalArr = array();
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($creditmemo);
            if ($total->canDisplay()) {
                $area = $total->getSourceField() == 'grand_total' ? 'footer' : 'body';
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $totalArr[$area][] = new \Magento\Framework\DataObject(array('label' => $totalData['label'], 'value' => $totalData['amount']));
                }
            }
        }
        $creditmemoData['totals'] = new \Magento\Framework\DataObject($totalArr);
        $apiKey = $this->helper->getApiKey($order->getStoreId(), $order->getCustomerGroupId());

        //check if order has invoice
        if($order->hasInvoices()) {
            $sourceData['invoice'] = new \Magento\Framework\DataObject(['increment_id' => $order->getInvoiceCollection()->getFirstItem()->getData('increment_id')]);
        }
        $creditmemoData = new \Magento\Framework\DataObject($creditmemoData);

        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', array('source' => $creditmemoData, 'model' => $creditmemo, 'type' => 'creditmemo'));

        $creditmemoData = new \Magento\Framework\DataObject(array('key' => $apiKey, 'data' => $creditmemoData));
        $this->revertTranslation();

        return $creditmemoData;
    }
}
