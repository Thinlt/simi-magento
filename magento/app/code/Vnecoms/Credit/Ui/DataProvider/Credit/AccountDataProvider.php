<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Ui\DataProvider\Credit;

use Vnecoms\Credit\Model\ResourceModel\Credit\CollectionFactory;

/**
 * Class ProductDataProvider
 */
class AccountDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;


    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->collection->join(
            array('customer_grid'=>$this->collection->getTable('customer_grid_flat')),
            "customer_grid.entity_id=main_table.customer_id",
            ['entity_id', 'name','email','group_id','website_id','created_at','billing_telephone','billing_postcode','billing_country_id','billing_region']
        );
    }
}
