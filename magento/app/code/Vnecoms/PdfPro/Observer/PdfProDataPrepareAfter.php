<?php

namespace Vnecoms\PdfPro\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\PdfPro\Helper\Data as Helper;
use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;

/**
 * Class PdfProDataPrepareAfter.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class PdfProDataPrepareAfter implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_designInterface;

    /**
     * @var \Vnecoms\PdfPro\Helper\Giftmessage
     */
    protected $giftHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_datetime;

    /**
     * @var DefaultTotal
     */
    protected $defaultTotal;
    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    protected $block;

    /**
     * PdfProDataPrepareAfter constructor.
     *
     * @param Helper                                                        $helper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface          $timezoneInterface
     * @param DefaultTotal                                                  $total
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface          $localeDate
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                   $dateTime
     * @param \Vnecoms\PdfPro\Helper\Giftmessage                            $giftmessage
     * @param \Magento\Framework\View\DesignInterface                       $designInterface
     */
    public function __construct(
        Helper $helper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        DefaultTotal $total,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Vnecoms\PdfPro\Helper\Giftmessage $giftmessage,
        \Magento\Framework\View\DesignInterface $designInterface,
        \Magento\Framework\View\Element\BlockFactory $blockFactory
    ) {
        $this->defaultTotal = $total;
        $this->_localeDate = $localeDate;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->_datetime = $dateTime;
        $this->giftHelper = $giftmessage;
        $this->_designInterface = $designInterface;
        $this->helper = $helper;
        $this->block = $blockFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $type = $observer->getType();
        if (in_array($type, array('order', 'invoice', 'shipment', 'creditmemo'))) {
            $source = $observer->getSource();
            $model = $observer->getModel();

            /*Add grand total exclude tax variable*/
            $baseGrandTotal = $model->getBaseGrandTotal();
            $grandTotal = $model->getGrandTotal();
            $baseTaxAmount = $model->getBaseTaxAmount();
            $taxAmount = $model->getTaxAmount();

            $baseGrandTotalExclTax = $baseGrandTotal - $baseTaxAmount;
            $grandTotalExclTax = $grandTotal - $taxAmount;

            $order = $type == 'order' ? $model : $model->getOrder();
            $orderCurrencyCode = $order->getOrderCurrencyCode();
            $baseCurrencyCode = $order->getBaseCurrencyCode();

            $source->setData('base_grand_total_excl_tax', $this->helper->currency($baseGrandTotalExclTax, $baseCurrencyCode));
            $source->setData('grand_total_excl_tax', $this->helper->currency($grandTotalExclTax, $orderCurrencyCode));

            /*Add tax summary as variables.*/
            $order = $model instanceof \Magento\Sales\Model\Order ? $model : $model->getOrder();
            $this->defaultTotal->setOrder($order);
            $taxSummary = array();

            $keys = '';
            $fullTaxInfo = $this->defaultTotal->getFullTaxInfo();
            if (is_array($fullTaxInfo) &&
                array_key_exists('percent', $fullTaxInfo) &&
                array_key_exists('tax_amount', $fullTaxInfo) &&
                array_key_exists('base_tax_amount', $fullTaxInfo)) {
                foreach ($fullTaxInfo as $tax) {

                //var_dump($tax);
                $key = $this->helper->formatKey($tax['label']);
                    $keys .= $key.'<br />';
                    $tax['percent'] *= 1.0;
                    $tax['tax_amount'] *= 1.0;
                    $tax['base_tax_amount'] *= 1.0;
                    $taxSummary[$key] = new \Magento\Framework\DataObject($tax);
                }
            }

            $taxSummary['all_keys'] = $keys;
            $taxSummary = new \Magento\Framework\DataObject($taxSummary);
            $source->setData('tax_summary', $taxSummary);

            /*Add printed date time variable*/
            $timestamp = $this->_datetime->timestamp();
            $date = $this->_localeDate->date($timestamp);

            $dateFormated = new \Magento\Framework\DataObject(array(
                'full' => $this->_localeDate->getDateFormat(\IntlDateFormatter::FULL),
                'long' => $this->_localeDate->getDateFormat(\IntlDateFormatter::LONG),
                'medium' => $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM),
                'short' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            ));
            $timeFormated = new \Magento\Framework\DataObject(array(
                'full' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::FULL),
                'long' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::LONG),
                'medium' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::MEDIUM),
                'short' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::SHORT),
            ));

            $source->setData('printed_date', $dateFormated);
            $source->setData('printed_time', $timeFormated);
        }

        if ($type == 'item') {
            $itemData = $observer->getSource();
            $item = $observer->getModel();
            if ($item instanceof \Magento\Sales\Model\Order\Item) {
                $order = $item->getOrder();
                $itemData->setData('giftmessage', $this->giftHelper->initMessage($item));
                $orderItem = $item;
            } elseif ($item instanceof \Magento\Sales\Model\Order\Invoice\Item) {
                $order = $item->getInvoice()->getOrder();
                $orderItem = $item->getOrderItem();
            } elseif ($item instanceof \Magento\Sales\Model\Order\Shipment\Item) {
                $order = $item->getShipment()->getOrder();
                $orderItem = $item->getOrderItem();
            } elseif ($item instanceof \Magento\Sales\Model\Order\Creditmemo\Item) {
                $order = $item->getCreditmemo()->getOrder();
                $orderItem = $item->getOrderItem();
            }
            $orderCurrencyCode = $order->getOrderCurrencyCode();
            $baseCurrencyCode = $order->getBaseCurrencyCode();
            $itemData->setData('weight', $item->getWeight() * 1);
            $itemData->setData('row_weight', $item->getRowWeight() * 1);
            $itemData->setData('is_virtual', $item->getIsVirtual());
            $itemData->setData('description', $item->getData('description'));

            if ($itemData->getData('price')) {
                $itemData->setData('price_incl_tax',
                    $this->helper->currency($item->getData('price_incl_tax'), $orderCurrencyCode));
                $itemData->setData('price_excl_tax',
                    $this->helper->currency($item->getData('price'), $orderCurrencyCode));
                $itemData->setData('row_total_incl_tax',
                    $this->helper->currency($item->getData('row_total_incl_tax'), $orderCurrencyCode));
                $itemData->setData('row_total_excl_tax',
                    $this->helper->currency($item->getData('row_total'), $orderCurrencyCode));
                $itemData->setData('discount_amount',
                    $this->helper->currency($item->getData('discount_amount'), $orderCurrencyCode));
                $itemData->setData('tax_percent', $this->helper->displayTaxPercent($orderItem));
                $itemData->setData('tax_rates', $this->helper->displayTaxPercent($orderItem));
                $itemData->setData('tax_amount',
                    $this->helper->currency($item->getData('tax_amount'), $orderCurrencyCode));
                $itemData->setData('discount_percent', $orderItem->getData('discount_percent'));
                $itemData->setData('discount_rates', $orderItem->getData('discount_percent'));
                $itemData->setData('row_total_incl_discount', $this->helper->currency($item->getData('row_total_incl_tax') - $orderItem->getData('discount_amount'), $orderCurrencyCode));
                $itemData->setData('row_total_incl_discount_excl_tax', $this->helper->currency($item->getData('row_total') - $orderItem->getData('discount_amount'), $orderCurrencyCode));

                //add row_total_incl_discount_and_tax
                $itemData->setData('row_total_incl_discount_and_tax', $this->helper->currency($item->getData('row_total'), $orderCurrencyCode));

                $itemData->setData('base_cost', $this->helper->currency($item->getData('base_cost'), $baseCurrencyCode));
                $itemData->setData('base_price', $this->helper->currency($item->getData('base_price'), $baseCurrencyCode));
                $itemData->setData('base_original_price', $this->helper->currency($item->getData('base_original_price'), $baseCurrencyCode));
                $itemData->setData('base_tax_amount', $this->helper->currency($item->getData('base_tax_amount'), $baseCurrencyCode));
                $itemData->setData('base_discount_amount', $this->helper->currency($item->getData('base_discount_amount'), $baseCurrencyCode));
                $itemData->setData('base_row_total', $this->helper->currency($item->getData('base_row_total'), $baseCurrencyCode));
                $itemData->setData('base_price_incl_tax', $this->helper->currency($item->getData('base_price_incl_tax'), $baseCurrencyCode));
                $itemData->setData('base_row_total_incl_tax', $this->helper->currency($item->getData('base_row_total_incl_tax'), $baseCurrencyCode));
                $itemData->setData('base_discount_amount', $this->helper->currency($item->getData('base_discount_amount'), $baseCurrencyCode));
            }

            if ($item instanceof \Magento\Sales\Model\Order\Item) {
                $itemData->setData('qty_backordered', $item->getData('qty_backordered') * 1);
                $itemData->setData('qty_canceled', $item->getData('qty_canceled') * 1);
                $itemData->setData('qty_invoiced', $item->getData('qty_invoiced') * 1);
                $itemData->setData('qty_ordered', $item->getData('qty_ordered') * 1);
                $itemData->setData('qty_refunded', $item->getData('qty_refunded') * 1);
                $itemData->setData('qty_shipped', $item->getData('qty_shipped') * 1);
            }
        } elseif ($type == 'order') {
            $orderData = $observer->getSource();
            $order = $observer->getModel();
            $orderData->setData('has_discount', $order->getDiscountAmount() < 0 ? 1 : 0);
            $this->addOrderComments($order, $orderData);

            $this->addAreaToObj($orderData);
        } elseif ($type == 'invoice') {
            $invoiceData = $observer->getSource();
            $invoice = $observer->getModel();
            $invoiceData->setData('has_discount', $invoice->getDiscountAmount() > 0 ? 1 : 0);

            $this->addComments($type, $invoice, $invoiceData);

            $this->addAreaToObj($invoiceData);
        } elseif ($type == 'shipment') {
            $shipmentData = $observer->getSource();
            $shipment = $observer->getModel();

            $this->addComments($type, $shipment, $shipmentData);
            $this->addAreaToObj($shipmentData);
            $trackingBlock = $this->block->createBlock('Magento\Framework\View\Element\Template');
            $trackingBlock->setData(array('tracking'=>$shipmentData->getTracking()))->setTemplate('Vnecoms_PdfPro::observer/tracking.phtml');

            $shipmentData->setData('tracking_html',$trackingBlock->toHtml());
        } elseif ($type == 'creditmemo') {
            $creditmemoData = $observer->getSource();
            $creditmemo = $observer->getModel();

            $creditmemoData->setData('has_discount', $creditmemo->getDiscountAmount() > 0 ? 1 : 0);
            $this->addComments($type, $creditmemo, $creditmemoData);

            $this->addAreaToObj($creditmemoData);
        } else {
            //nothing
        }
    }

    /**
     * add order comments.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param $orderData
     */
    public function addOrderComments(\Magento\Sales\Model\Order $order, $orderData)
    {
        $comments = array();
        foreach ($order->getStatusHistoryCollection(true) as $item) {
            $_item = new \Magento\Framework\DataObject($item->getData());
            $_item->setData('created_date', $this->helper->getFormatedDate($item->getCreatedAtDate(), 'medium'));
            $_item->setData('created_time', $this->helper->getFormatedTime($item->getCreatedAtDate(), 'medium'));
            $_item->setData('status', $item->getStatusLabel());
            switch ($item->getData('is_customer_notified')) {
                case '0':
                    $_item->setData('customer_notified', __('Not Notified'));
                    break;
                case '1':
                    $_item->setData('customer_notified', __('Notified'));
                    break;
                case '2':
                    $_item->setData('customer_notified', __('Notification Not Applicable'));
                    break;
            }
            $comments[] = $_item;
        }
        $orderData->setData('comments', $comments);
    }

    /**
     * Add comments in other document
     *
     * @param $type
     * @param $model
     * @param $source
     */
    public function addComments($type, $model, $source)
    {
        $comments = array();
        foreach ($model->getCommentsCollection(true) as $comment) {
            $_item = new \Magento\Framework\DataObject($comment->getData());
            $_item->setData('created_date', $this->helper->getFormatedDate($comment->getCreatedAtDate(), 'medium'));
            $_item->setData('created_time', $this->helper->getFormatedTime($comment->getCreatedAtDate(), 'medium'));
            switch ($comment->getData('is_customer_notified')) {
                case '0':
                    $_item->setData('customer_notified', __('Not Notified'));
                    break;
                case '1':
                    $_item->setData('customer_notified', __('Notified'));
                    break;
                case '2':
                    $_item->setData('customer_notified', __('Notification Not Applicable'));
                    break;
            }
            $comments[] = $_item;
        }
        $source->setData('comments', $comments);
    }

    /**
     * Add area variable to objects :order, invoice, shipment, creditmemo.
     *
     * @param $source
     */
    public function addAreaToObj($source)
    {
        $source->setIsPrintedFromFrontend($this->_designInterface->getArea() == 'frontend');
    }
}
