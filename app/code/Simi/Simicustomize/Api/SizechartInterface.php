<?php
/**
 * Copyright © Magento, Inc. All rights Size chart.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Api;

interface SizechartInterface
{
    /**
     * Save Sizechart request
     * @return boolean
     */
    public function index();

    /**
     * Get Sizecharts by customer
     * @return array
     */
    public function getSizecharts();
}