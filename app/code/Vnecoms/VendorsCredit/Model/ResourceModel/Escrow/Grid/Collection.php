<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Model\ResourceModel\Escrow\Grid;

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
        $mainTable = 'ves_vendor_escrow';
        $resourceModel = 'Vnecoms\VendorsCredit\Model\ResourceModel\Escrow';
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }


    protected function _construct()
    {
        parent::_construct();
        $fields = [
            'status',
            'created_at',
            'updated_at',
            'vendor_id'
        ];
        foreach ($fields as $field) {
            $this->addFilterToMap(
                $field,
                'main_table.'.$field
            );
        }
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
            ['vendor'=>$this->getTable('ves_vendor_entity')],
            'vendor.entity_id = main_table.vendor_id',
            ['vendor' => 'vendor_id'],
            null,
            'left'
        );
        $this->join(
            ['vendor_invoice'=>$this->getTable('ves_vendor_sales_invoice')],
            'vendor_invoice.entity_id = main_table.relation_id',
            ['invoice_id' => 'invoice_id'],
            null,
            'left'
        );
        $this->join(
            ['invoice'=>$this->getTable('sales_invoice')],
            'vendor_invoice.invoice_id = invoice.entity_id',
            ['increment_id' => 'increment_id'],
            null,
            'left'
        );
        return $this;
    }
}
