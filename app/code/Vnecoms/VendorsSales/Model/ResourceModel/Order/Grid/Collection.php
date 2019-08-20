<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\ResourceModel\Order\Grid;

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
        $mainTable = 'ves_vendor_sales_order';
        $resourceModel = 'Magento\Sales\Model\ResourceModel\Order';
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
            'created_at',
            'entity_id'
        ];
        foreach ($fields as $field) {
            $this->addFilterToMap(
                $field,
                'main_table.'.$field
            );
        }

        $orderField = [
            'increment_id',
        ];
        foreach($orderField as $field){
            $this->addFilterToMap(
                $field,
                'order_grid.'.$field
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
            ['order_grid' => $this->getTable('sales_order_grid')],
            'main_table.order_id=order_grid.entity_id',
            [
                'increment_id',
                'store_id',
                'store_name',
                'base_currency_code',
                'order_currency_code',
                'shipping_name',
                'billing_name',
                'billing_address',
                'shipping_address',
                'shipping_and_handling',
                'total_refunded',
                'customer_name',
                'customer_email',
                'customer_group',
                'payment_method',
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
