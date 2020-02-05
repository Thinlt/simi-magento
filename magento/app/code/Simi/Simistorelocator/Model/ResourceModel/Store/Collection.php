<?php

namespace Simi\Simistorelocator\Model\ResourceModel\Store;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'simistorelocator_id';

    /**
     * @var bool
     */
    public $loadBaseImage;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\Json\Helper\Data                          $jsonHelper
     * @param bool|FALSE                                                   $loadBaseimage
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|NULL          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|NULL    $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        $loadBaseimage = false,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->loadBaseImage = $loadBaseimage;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return bool
     */
    public function isLoadBaseImage()
    {
        return $this->loadBaseImage;
    }

    /**
     * @param bool $loadBaseImage
     */
    public function setLoadBaseImage($loadBaseImage)
    {
        $this->loadBaseImage = $loadBaseImage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Simi\Simistorelocator\Model\Store', 'Simi\Simistorelocator\Model\ResourceModel\Store');
    }

    /**
     * Add latitude and longitude to filter by distance.
     *
     * @param      $lat
     * @param      $lng
     * @param null $distance
     *
     * @return $this
     */
    public function addLatLngToFilterDistance($lat, $lng, $distance = null)
    {
        $expression = "(1609.34*((acos(sin(({{lat}}*pi()/180)) * sin((`{{latitude}}`*pi()/180))+cos(($lat *pi()/180)) * cos((`{{latitude}}`*pi()/180)) * cos((({{lng}} - `{{longitude}}`)*pi()/180))))*180/pi())*60*1.1515)";
        $this->addExpressionFieldToSelect('distance', $expression, ['latitude' => 'latitude', 'longitude' => 'longitude', 'lat' => $lat, 'lng' => $lng]);

        if ($distance) {
            $this->getSelect()->having('distance <= ?', $distance);
        }

        return $this;
    }

    /**
     * Filter by tags.
     *
     * @param array $tagIds
     *
     * @return $this
     */
    public function addTagsToFilter(array $tagIds = [])
    {
        $connection = $this->getResource()->getConnection();

        $select = $connection->select()->from(
            $this->getTable(\Simi\Simistorelocator\Setup\InstallSchema::SCHEMA_STORE_TAG),
            'simistorelocator_id'
        )->where(
            'tag_id IN(?)',
            $tagIds
        );
        $this->addFieldToFilter('simistorelocator_id', ['in' => $connection->fetchCol($select)]);

        return $this;
    }

    /**
     * @return \Magento\Framework\DB\Select
     *
     * @throws \Zend_Db_Select_Exception
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Zend_Db_Select::ORDER);
        $countSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(\Zend_Db_Select::COLUMNS);

        if (count($this->getSelect()->getPart(\Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(\Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(\Zend_Db_Select::GROUP);
            $countSelect->columns('COUNT(DISTINCT ' . implode(', ', $group) . ')');
        } elseif (count($this->getSelect()->getPart(\Zend_Db_Select::HAVING)) > 0) {
            $connection = $this->getResource()->getConnection();

            return $connection->select()->from(['select_store' => $this->getSelect()], 'COUNT(*)');
        } else {
            $countSelect->columns('COUNT(*)');
        }

        return $countSelect;
    }

    /**
     * prepa to json
     *
     * @param array $mapJsonFields
     */
    public function prepareJson()
    {
        $storeArray = [];

        foreach ($this as $item) {
            $storeArray[] = $item->getData();
        }

        return $storeArray;
    }

    /**
     * Before load action
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        if ($this->isLoadBaseImage()) {
            $this->getSelect()->joinLeft(
                ['tableImage' => $this->getTable(\Simi\Simistorelocator\Setup\InstallSchema::SCHEMA_IMAGE)],
                'main_table.baseimage_id = tableImage.image_id', ['baseimage' => 'tableImage.path']
            );
        }
        return parent::_beforeLoad();
    }

    public function applyAPICollectionFilter($visibilityTable, $typeID, $storeID)
    {
        $this->getSelect()
            ->join(
                ['visibility' => $visibilityTable],
                'visibility.item_id = main_table.simistorelocator_id AND visibility.content_type = ' . $typeID
                . ' AND visibility.store_view_id =' . $storeID
            );
        return $this;
    }
}
