<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\ResourceModel\Vendor;

/**
 * App page collection
 */
class Collection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    /**
     * Name of collection model
     */
    const VENDOR_MODEL_NAME = 'Vnecoms\Vendors\Model\Vendor';

    /**
     * @var \Magento\Framework\DataObject\Copy\Config
     */
    protected $_fieldsetConfig;

    /**
     * @var string
     */
    protected $_modelName;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
     * @param mixed $connection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->entitySnapshot = $entitySnapshot;

        $this->_modelName = self::VENDOR_MODEL_NAME;
        
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $connection
        );
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init($this->_modelName, 'Vnecoms\Vendors\Model\ResourceModel\Vendor');
    }
    
    /**
     * Init select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinTable(
            ['vendor_user'=>$this->getTable('ves_vendor_user')],
            'vendor_id=entity_id',
            ['is_super_user'=>'is_super_user','vendor_user_customer_id'=>'customer_id'],
            ['is_super_user' => 1]
        );
        $this->joinTable(
            ['customer'=>$this->getTable('customer_entity')],
            'entity_id=vendor_user_customer_id',
            ['firstname'=>'firstname','lastname'=>'lastname','middlename'=>'middlename','email'=>'email']
        );
//             $this->getSelect()->join(
//                 array('vendor_user'=>$this->getTable('ves_vendor_user')),
//                 'e.entity_id=vendor_user.vendor_id',
//                 array()
//             );
//             $this->getSelect()->join(
//                 array('customer'=>$this->getTable('customer_entity')),
//                 'vendor_user.customer_id=customer.entity_id',
//                 array('firstname','lastname','middlename','email')
//             );
        return $this;
    }
}
