<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\ResourceModel\Order\Invoice\Grid;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Psr\Log\LoggerInterface as Logger;

/**
 * App page collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager
    ) {
        $mainTable = 'ves_vendor_sales_invoice';
        $resourceModel = 'Magento\Sales\Model\ResourceModel\Order\Invoice';
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $fields = [
            'status',
            'grand_total',
            'base_grand_total',
            'vendor_id',
            'entity_id',
            'created_at',
            'state'
        ];
        
        foreach ($fields as $field) {
            $this->addFilterToMap(
                $field,
                'main_table.'.$field
            );
        }

        // map field increment_id to filter
        $invoiceField = [
            'increment_id',
        ];

        foreach($invoiceField as $field){
            $this->addFilterToMap(
                $field,
                'invoice_grid.'.$field
            );
        }

        $this->addFilterToMap(
            'vendor',
            'vendor_tbl.vendor_id'
        );
    }
    
    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->join(
            [
            'invoice_grid'=>$this->getTable('sales_invoice_grid')],
            'main_table.invoice_id=invoice_grid.entity_id',
            [
                'increment_id',
                'order_increment_id',
                'store_id',
                'customer_name',
                'billing_name',
                'billing_address',
                'shipping_address',
                'store_currency_code',
                'order_currency_code',
                'base_currency_code',
                'global_currency_code',
                'created_at',
                'order_created_at'
            ]
        );
        $this->join(
            ['vendor_tbl' => $this->getTable('ves_vendor_entity')],
            'main_table.vendor_id=vendor_tbl.entity_id',
            [
                'vendor' => 'vendor_id',
            ]
        );
            return $this;
    }
}
