<?php
/**
 * Copyright 2019 magento. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\Simicustomize\Model\ResourceModel\SizeChart;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Simi\Simicustomize\Model\ResourceModel\SizeChart
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Simi\Simicustomize\Model\SizeChart::class, \Simi\Simicustomize\Model\ResourceModel\SizeChart::class);
    }
}
