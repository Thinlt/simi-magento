<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Api;

interface VendorListInterface
{
    /**
     * Vendor list api VnecomsVendor module
     * @return array | json
     */
    public function getVendorList();
}