<?php


namespace Simi\Simistorelocator\Model\ResourceModel;


abstract class AbstractDbManageStores extends \Simi\Simistorelocator\Model\ResourceModel\AbstractResource
        implements \Simi\Simistorelocator\Model\ResourceModel\DbManageStoresInterface
{
    /**
     * @var \Simi\Simistorelocator\Model\ResourceModel\Store\CollectionFactory
     */
    public $storeCollectionFactory;

    /**
     * Class constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null                                  $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Simi\Simistorelocator\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->storeCollectionFactory = $storeCollectionFactory;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param array                                  $storelocatorIds
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function pickStores(\Magento\Framework\Model\AbstractModel $object, array $storelocatorIds = [])
    {
        $id = (int) $object->getId();
        $table = $this->getStoreRelationTable();

        $old = $this->getStorelocatorIds($object);
        $new = $storelocatorIds;

        /*
         * remove stores from object
         */
        $this->deleteData(
            $table,
            [
                $this->getIdFieldName() . ' = ?' => $id,
                'simistorelocator_id IN(?)' => array_values(array_diff($old, $new)),
            ]
        );

        /*
         * add store to object
         */
        $insert = [];
        foreach (array_values(array_diff($new, $old)) as $storelocatorId) {
            $insert[] = [$this->getIdFieldName() => $id, 'simistorelocator_id' => (int) $storelocatorId];
        }
        $this->insertData($table, $insert);

        return $this;
    }

    /**
     * get collection store of model.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getStores(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \Simi\Simistorelocator\Model\ResourceModel\Store\Collection $collection */
        $collection = $this->_storeCollectionFactory->create();
        $collection->addFieldToFilter(
            'simistorelocator_id',
            ['in' => $this->getStorelocatorIds($object)]
        );

        return $collection;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return mixed
     */
    public function getStorelocatorIds(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $id = (int) $object->getId();

        $select = $connection->select()->from(
            $this->getStoreRelationTable(),
            'simistorelocator_id'
        )->where(
            $this->getIdFieldName() . ' = :object_id'
        );

        return $connection->fetchCol($select, [':object_id' => $id]);
    }

    /**
     * get table relation ship.
     *
     * @return string
     */
    abstract public function getStoreRelationTable();
}
