<?php

namespace Simi\Simistorelocator\Model\ResourceModel;

class Schedule extends \Simi\Simistorelocator\Model\ResourceModel\AbstractResource implements \Simi\Simistorelocator\Model\ResourceModel\DbManageStoresInterface {

    /**
     * @var \Simi\Simistorelocator\Model\ResourceModel\Store\CollectionFactory
     */
    public $simiObjectManager;

    /**
     * Class constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null                                  $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function _construct() {
        $this->_init(\Simi\Simistorelocator\Setup\InstallSchema::SCHEMA_SCHEDULE, 'schedule_id');
    }

    /**
     * pick stores for model.
     *
     * @param array $storelocatorIds
     *
     * @return mixed
     */
    public function pickStores(\Magento\Framework\Model\AbstractModel $object, array $storelocatorIds = []) {
        $id = (int) $object->getId();

        $table = $this->getTable(\Simi\Simistorelocator\Setup\InstallSchema::SCHEMA_STORE);
        $old = $this->getStorelocatorIds($object);
        $new = $storelocatorIds;

        /*
         * remove stores from schedule
         */
        $bind = [$this->getIdFieldName() => new \Zend_Db_Expr('NULL')];
        $where = ['simistorelocator_id IN(?)' => array_values(array_diff($old, $new))];
        $this->updateData($table, $bind, $where);

        /*
         * add stores to schedule
         */
        $bind = [$this->getIdFieldName() => new \Zend_Db_Expr($id)];
        $where = ['simistorelocator_id IN(?)' => array_values(array_diff($new, $old))];
        $this->updateData($table, $bind, $where);

        return $this;
    }

    /**
     * get collection store of model.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getStores(\Magento\Framework\Model\AbstractModel $object) {
        /** @var \Simi\Simistorelocator\Model\ResourceModel\Store\Collection $collection */
        $collection = $this->simiObjectManager->create('\Simi\Simistorelocator\Model\Store')->getCollection();
        $collection->addFieldToSelect('simistorelocator_id')
                ->addFieldToFilter($this->getIdFieldName(), (int) $object->getId());

        return $collection;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return mixed
     */
    public function getStorelocatorIds(\Magento\Framework\Model\AbstractModel $object) {
        /** @var \Simi\Simistorelocator\Model\ResourceModel\Store\Collection $collection */
        $collection = $this->getStores($object);

        return $collection->getAllIds();
    }

}
