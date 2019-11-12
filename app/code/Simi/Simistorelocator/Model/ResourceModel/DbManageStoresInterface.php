<?php

namespace Simi\Simistorelocator\Model\ResourceModel;

interface DbManageStoresInterface {

    /**
     * pick stores for model.
     *
     * @param array $storelocatorIds
     *
     * @return mixed
     */
    public function pickStores(\Magento\Framework\Model\AbstractModel $object, array $storelocatorIds = []);

    /**
     * get collection store of model.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getStores(\Magento\Framework\Model\AbstractModel $object);

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return mixed
     */
    public function getStorelocatorIds(\Magento\Framework\Model\AbstractModel $object);
}
