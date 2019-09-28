<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Api;

interface VendorLoginInterface
{
    /**
     * Vendor login api VnecomsVendor module
     * @return array | json
     */
    public function loginPost();
}