<?php

namespace Vnecoms\PdfPro\Model\Order;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class Shipment.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Shipment extends \Vnecoms\PdfPro\Model\AbstractPdf
{
    /**
     * @var \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
     */
    protected $_defaultTotalModel;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_helperPayment;

    /**
     * @var \Vnecoms\PdfPro\Helper\Giftmessage
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
     * @var \Vnecoms\PdfPro\Model\Order\Shipment\ItemFactory
     */
    protected $shipmentItemFactory;

    /**
     * Order constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                $localeDate
     * @param \Vnecoms\PdfPro\Helper\Data                                         $helper
     * @param ListsInterface                                                      $listsInterface
     * @param ManagerInterface                                                    $event
     * @param \Magento\Framework\Locale\Resolver                                  $locale
     * @param \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal                   $defaultTotal
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManagerInterface
     * @param \Vnecoms\PdfPro\Helper\Giftmessage                                  $giftmessage
     * @param \Magento\Payment\Helper\Data                                        $helperPayment
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $option
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface       $dateTimeFormatterInterface
     * @param \Magento\Sales\Model\Order\Pdf\Config                               $config
     * @param \Magento\Framework\Logger\Monolog                                   $logger
     * @param \Magento\Store\Model\App\Emulation                                  $emulation
     * @param \Vnecoms\PdfPro\Model\Order\Shipment\ItemFactory                    $shipmentItemFactory
     * @param array                                                               $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Vnecoms\PdfPro\Helper\Data $helper,
        ListsInterface $listsInterface,
        ManagerInterface $event,
        \Magento\Framework\Locale\Resolver $locale,
        \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal $defaultTotal,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Vnecoms\PdfPro\Helper\Giftmessage $giftmessage,
        \Magento\Payment\Helper\Data $helperPayment,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $option,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatterInterface,
        \Magento\Sales\Model\Order\Pdf\Config $config,
        \Magento\Framework\Logger\Monolog $logger,
        \Magento\Store\Model\App\Emulation $emulation,
        \Vnecoms\PdfPro\Model\Order\Shipment\ItemFactory $shipmentItemFactory,
        array $data = []
    ) {
        $this->_defaultTotalModel = $defaultTotal;
        $this->giftHelper = $giftmessage;
        $this->_helperPayment = $helperPayment;
        $this->pdfConfig = $config;
        $this->logger = $logger;
        $this->shipmentItemFactory = $shipmentItemFactory;
        parent::__construct($localeDate, $helper, $listsInterface, $event, $locale, $storeManagerInterface, $option, $dateTimeFormatterInterface,$emulation, $data);
    }

    /**
     * initial shipment data.
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     *
     * @return string
     */
    public function initShipmentData(\Magento\Sales\Model\Order\Shipment $shipment)
    {
        $shipmentData = $shipment->getData();
        unset($shipmentData['shipping_label']);

        $order = $shipment->getOrder();
        $this->setTranslationByStoreId($shipment->getStoreId());

        $orderData = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Vnecoms\PdfPro\Model\Order')->initOrderData($order);

        $shipmentData['order'] = ($orderData);
        $shipmentData['customer'] = $this->getCustomerData(\Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Customer\Model\Customer')->load($order->getCustomerId()));
        $shipmentData['created_at_formated'] = $this->getFormatedDate($shipment->getCreatedAt());
        $shipmentData['updated_at_formated'] = $this->getFormatedDate($shipment->getUpdatedAt());

        $shipmentData['billing'] = $this->getAddressData($shipment->getBillingAddress());
        /*if order is not virtual */
        if (!$order->getIsVirtual()) {
            $shipmentData['shipping'] = $this->getAddressData($shipment->getShippingAddress());
        }

        /*Get Payment Info */
        $paymentInfo = $this->_helperPayment->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->setArea(\Magento\Framework\App\Area::AREA_ADMINHTML)
            ->toPdf();

        $paymentInfo = str_replace('{{pdf_row_separator}}', '<br />', $paymentInfo);
        $shipmentData['payment'] =
            array('code' => $order->getPayment()->getMethodInstance()->getCode(),
                'name' => $order->getPayment()->getMethodInstance()->getTitle(),
                'info' => $paymentInfo,
            );
        $shipmentData['payment_info'] = $paymentInfo;
        $shipmentData['shipping_description'] = $order->getShippingDescription();

        /*Get Tracks*/
        $tracks = array();
        foreach ($shipment->getAllTracks() as $track) {
            $tracks[] = new \Magento\Framework\DataObject($track->getData());
        }
        $shipmentData['tracking'] = sizeof($tracks) ? $tracks : false;
        $shipmentData['items'] = array();
        $orderCurrencyCode = $order->getOrderCurrencyCode();

        /*
    	 * Get Items information
    	*/

        foreach ($shipment->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }

            /*
             * @var \Vnecoms\PdfPro\Model\Order\Shipment\Item $itemModel
             */
            $itemModel = $this->shipmentItemFactory->create(['data' => ['item' => $item]]);
            if ($item->getOrderItem()->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                $itemData = array('is_bundle' => 1, 'name' => $item->getName(), 'sku' => $item->getSku());
                $itemData['qty'] = $item->getQty() * 1;
                $itemData['sub_items'] = array();
                $shipItems = $itemModel->getChilds($item);
                $items = array_merge(array($item->getOrderItem()), $item->getOrderItem()->getChildrenItems());

                foreach ($items as $_item) {
                    $bundleItem = array();
                    $attributes = $itemModel->getSelectionAttributes($_item);
                    // draw SKUs
                    if (!$_item->getParentItem()) {
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
                    if (($itemModel->isShipmentSeparately() && $_item->getParentItem())
                        || (!$itemModel->isShipmentSeparately() && !$_item->getParentItem())
                    ) {
                        if (isset($shipItems[$_item->getId()])) {
                            $qty = $shipItems[$_item->getId()]->getQty() * 1;
                        } elseif ($_item->getIsVirtual()) {
                            $qty = __('N/A');
                        } else {
                            $qty = 0;
                        }
                    } else {
                        $qty = '';
                    }
                    $bundleItem['qty'] = $qty;
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
            $shipmentData['items'][] = $itemData;
        }

        //check if order has invoice
        if($order->hasInvoices()) {
            $sourceData['invoice'] = new \Magento\Framework\DataObject(['increment_id' => $order->getInvoiceCollection()->getFirstItem()->getData('increment_id')]);
        }
        $apiKey = $this->helper->getApiKey($order->getStoreId(), $order->getCustomerGroupId());
        $shipmentData = new \Magento\Framework\DataObject($shipmentData);

        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', array('source' => $shipmentData, 'model' => $shipment, 'type' => 'shipment'));

        $shipmentData = new \Magento\Framework\DataObject(array('key' => $apiKey, 'data' => $shipmentData));
        $this->revertTranslation();

        return $shipmentData;
    }
}
