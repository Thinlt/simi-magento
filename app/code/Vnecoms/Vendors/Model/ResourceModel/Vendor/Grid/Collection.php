<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\ResourceModel\Vendor\Grid;

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
        $mainTable = 'ves_vendor_entity';
        $resourceModel = 'Vnecoms\Vendors\Model\ResourceModel\Vendor';
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->_eventManager->dispatch('vnecoms_vendors_ui_accountdataprovider_collection_prepare', ['collection' => $this]);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->addFilterToMap(
            "vendor_id",
            'main_table.vendor_id'
        );

        $this->addFilterToMap(
            "group_id",
            'main_table.group_id'
        );

        $this->addFilterToMap(
            "entity_id",
            'main_table.entity_id'
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
        $this->getSelect()->join(
            ['vendor_user'=>$this->getTable('ves_vendor_user')],
            'vendor_user.vendor_id = main_table.entity_id AND is_super_user = 1',
            ['is_super_user'=>'is_super_user','vendor_user_id'=>'vendor_id']
        );

        $this->getSelect()->join(
            ['customer'=>$this->getTable('customer_entity')],
            'customer.entity_id=vendor_user.customer_id',
            ['firstname'=>'firstname','lastname'=>'lastname','middlename'=>'middlename','email'=>'email', 'web_id' => 'website_id', 'store_id' => 'store_id']
        );
        return $this;
    }
}
