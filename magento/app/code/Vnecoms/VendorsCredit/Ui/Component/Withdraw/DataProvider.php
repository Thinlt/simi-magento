<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Ui\Component\Withdraw;

use Vnecoms\VendorsCredit\Model\ResourceModel\Withdrawal\CollectionFactory;

/**
 * Class ProductDataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * Product collection
     *
     * @var \Vnecoms\VendorsReview\Model\ResourceModel\Review\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_session;
    
    /**
     * Construct
     * @param \Vnecoms\Vendors\Model\Session $session
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
        \Vnecoms\Vendors\Model\Session $session,
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->_session = $session;
        
        $this->collection = $collectionFactory->create();
        //$this->collection->addFieldToFilter('main_table.vendor_id',$this->_session->getVendor()->getId());
        $this->collection->join(['vendor'=>$this->collection->getTable('ves_vendor_entity')], 'vendor.entity_id = main_table.vendor_id', ['vendor' => 'vendor_id'], null, 'left');
        
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }
}
