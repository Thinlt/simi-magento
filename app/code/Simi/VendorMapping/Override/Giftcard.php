<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Override;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Model
 */
class Giftcard extends \Aheadworks\Giftcard\Model\Giftcard
{
    /**
     * {@inheritdoc}
     */
    public function getVendorId()
    {
        return $this->getData('vendor_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setVendorId($vendor_id)
    {
        return $this->setData('vendor_id', $vendor_id);
    }
}
