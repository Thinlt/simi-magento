<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Product\CollectionFactory as GiftcardProductCollectionFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Product\Collection;
use Vnecoms\Vendors\Model\Session;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\Giftcard\Ui\DataProvider\Product
 */
class ListingDataProvider extends \Aheadworks\Giftcard\Ui\DataProvider\Product\ListingDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

     /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $vendorSession;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ProductCollectionFactory $productCollectionFactory
     * @param GiftcardProductCollectionFactory $giftcardProductCollectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ProductCollectionFactory $productCollectionFactory,
        GiftcardProductCollectionFactory $giftcardProductCollectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = [],
        Session $vendorSession
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $productCollectionFactory,
            $giftcardProductCollectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->vendorSession = $vendorSession;
        $this->collection = $giftcardProductCollectionFactory->create();
        $this->collection->addFieldToFilter('vendor_id', $this->vendorSession->getVendor()->getId()); //vendor entity_id
    }
}
