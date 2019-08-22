<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Ui\DataProvider\Giftcode;

use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Collection;

/**
 * Class ProductDataProvider
 */
class GiftcodeDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

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
        // CollectionFactory $collectionFactory,
        // array $addFieldStrategies = [],
        // array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
