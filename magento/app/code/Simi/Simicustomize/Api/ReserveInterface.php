<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Api;

interface ReserveInterface
{
    /**
     * Save Reserve request
     * @return boolean
     */
    public function index();

    /**
     * Get my reserved products
     * @return array|boolean
     */
    public function getMyReserved();

    /**
     * Remove my reserved product
     * 
     * @param int $id
     * @return array|boolean
     */
    public function cancelMyReserved($id);
}