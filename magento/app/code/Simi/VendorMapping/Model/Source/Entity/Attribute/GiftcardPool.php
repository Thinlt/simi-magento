<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Model\Source\Entity\Attribute;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Pool\Collection as PoolCollection;
use Vnecoms\Vendors\Model\Session;

/**
 * Class GiftcardPool
 *
 * @package Aheadworks\Giftcard\Model\Source\Entity\Attribute
 */
class GiftcardPool extends \Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardPool
{
    /**
     * @var PoolCollection
     */
    private $poolCollection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeFactory
     */
    private $eavAttributeFactory;

    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $vendorSession;

    /**
     * @param PoolCollection $poolCollection
     * @param MetadataPool $metadataPool
     * @param AttributeFactory $eavAttributeFactory
     */
    public function __construct(
        PoolCollection $poolCollection,
        MetadataPool $metadataPool,
        AttributeFactory $eavAttributeFactory,
        Session $vendorSession
    ) {
        $this->poolCollection = $poolCollection;
        $this->metadataPool = $metadataPool;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->vendorSession = $vendorSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->poolCollection
                ->addFieldToFilter('vendor_id', $this->vendorSession->getVendor()->getVendorId())
                ->toOptionArray();
        }
        return $this->_options;
    }
}
