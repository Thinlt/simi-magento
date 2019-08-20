<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\ResourceModel\Order\Creditmemo\Grid;

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
        $mainTable = 'sales_creditmemo_grid';
        $resourceModel = 'Magento\Sales\Model\ResourceModel\Order\Creditmemo';
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _construct()
    {
        parent::_construct();
        $fields = [
            'entity_id',
	    'order_id'
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
        $this->join(['ves_order_grid'=>$this->getTable('ves_vendor_sales_order')], 'main_table.vendor_order_id = ves_order_grid.entity_id', []);
        return $this;
    }
}
