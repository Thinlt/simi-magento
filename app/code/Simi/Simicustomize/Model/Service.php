<?php
/**
 * Copyright 2019 magento. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Simi\Simicustomize\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Giftcard
 *
 * @package Simi\Simicustomize\Model\Service
 */
class Service extends AbstractModel
{
    /**
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Simi\Simicustomize\Model\ResourceModel\Service::class);
    }
}
