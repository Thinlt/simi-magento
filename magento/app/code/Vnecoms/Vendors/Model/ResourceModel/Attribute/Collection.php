<?php
namespace Vnecoms\Vendors\Model\ResourceModel\Attribute;

class Collection extends \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
{
    /**
     * @var \Magento\Eav\Model\EntityFactory
     */
    protected $_eavEntityFactory;
    
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_eavEntityFactory = $eavEntityFactory;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $eavConfig, $connection, $resource);
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Vnecoms\Vendors\Model\Attribute',
            'Magento\Eav\Model\ResourceModel\Entity\Attribute'
        );
    }
    

    /**
     * @return $this
     */

    protected function _initSelect()
    {
        $eTypeId = (int)$this->_eavEntityFactory->create()->setType(
            \Vnecoms\Vendors\Model\Vendor::ENTITY
        )->getTypeId();
        $cols = $this->getConnection()
                    ->describeTable($this->getResource()
                    ->getMainTable());
        unset($cols['attribute_id']);
        $retCols = [];
        foreach ($cols as $labelCol => $colData) {
            $retCols[$labelCol] = $labelCol;
            if ($colData['DATA_TYPE'] == \Magento\Framework\DB\Ddl\Table::TYPE_TEXT) {
                $retCols[$labelCol] = 'main_table.' . $labelCol;
            }
        }
        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainTable()],
            $retCols
        )->join(
            ['attr_additional_table' => $this->getTable('ves_vendor_eav_attribute')],
            'attr_additional_table.attribute_id = main_table.attribute_id'
        )->where(
            'main_table.entity_type_id = ?',
            $eTypeId
        );
        return $this;
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return $this
     * @codeCoverageIgnore
     */
    public function addVisibleFilter()
    {
        return $this->addFieldToFilter('is_visible', 1);
    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $typeId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }
}
