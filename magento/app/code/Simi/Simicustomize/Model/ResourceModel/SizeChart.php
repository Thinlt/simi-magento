<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\Simicustomize\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel
 */
class SizeChart extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('simi_size_chart', 'id');
    }
}
