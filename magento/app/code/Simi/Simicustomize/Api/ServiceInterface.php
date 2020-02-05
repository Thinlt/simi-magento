<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Api;

interface ServiceInterface
{
    /**
     * Save Service request
     * @return boolean|array
     */
    public function save();
}