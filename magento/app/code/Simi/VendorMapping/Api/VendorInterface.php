<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Api;

interface VendorInterface
{
    /**
     * Vendor api VnecomsVendor module
     * @param int $id The Vendor ID.
     * @return array | json
     */
    public function getVendorDetail($id);

    /**
     * Vendor api VnecomsVendor module
     * @param int $id The Vendor ID.
     * @return array | json
     */
    public function getVendorReviews($id);
}