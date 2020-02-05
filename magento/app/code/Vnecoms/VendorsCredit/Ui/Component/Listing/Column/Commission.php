<?php

namespace Vnecoms\VendorsCredit\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Price
 */
class Commission extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = []
    ) {
        $this->priceFormatter = $priceFormatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @return float
     */
    protected function getOrderCommission($orderId)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $om->create('Magento\Sales\Model\Order')->load($orderId);
        $orderItemIds = $order->getItemsCollection()->getAllIds();
        
        $invoiceItemIds = [];
        $invoiceItemCollection = $om->create('Magento\Sales\Model\ResourceModel\Order\Invoice\Item\Collection');
        $invoiceItemCollection->addFieldToFilter('order_item_id', ['in' => $orderItemIds]);
        $invoiceItemIds = $invoiceItemCollection->getAllIds();
    
        $invoiceItems =[];
        foreach($invoiceItemIds as $invoiceItemId){
            $invoiceItems[] = 'invoice_item|'.$invoiceItemId;
        }
    
        $creditTransactionCollection = $om->create('Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\Collection');
        $creditTransactionCollection->addFieldToFilter('additional_info',['in' => $invoiceItems]);
        $amount = 0;
        foreach($creditTransactionCollection as $trans){
            $amount += $trans->getAmount();
        }
        $baseCommission = abs($amount);
    
        $commission = $order->getBaseCurrency()->convert($baseCommission, $order->getOrderCurrencyCode());
        return $commission;
    }
    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $orderId = $item['entity_id'];
                
                $currencyCode = isset($item['base_currency_code']) ? $item['base_currency_code'] : null;
                $item[$this->getData('name')] = $this->priceFormatter->format(
                    $this->getOrderCommission($orderId),
                    false,
                    null,
                    null,
                    $currencyCode
                );
            }
        }

        return $dataSource;
    }
}
