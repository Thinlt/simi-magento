<?php
/**
 * Copyright 2019 magento. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\Simicustomize\Model\ResourceModel\Reserve;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Giftcard
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Simi\Simicustomize\Model\Reserve::class, \Simi\Simicustomize\Model\ResourceModel\Reserve::class);
    }
}
