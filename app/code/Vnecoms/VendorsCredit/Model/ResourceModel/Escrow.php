<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Model\ResourceModel;

use Vnecoms\VendorsCredit\Model\Withdrawal as WithdrawalCredit;
/**
 * Cms page mysql resource
 */
class Escrow extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_escrow', 'escrow_id');
    }

    /**
     * Get total pending credit of a vendor
     *
     * @param int|\Vnecoms\Vendors\Model\Vendor $vendorId
     */
    public function getTotalPendingCredit($vendorId){
        if($vendorId instanceof \Vnecoms\Vendors\Model\Vendor)
            $vendorId = $vendorId->getId();

        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_escrow'),
            ['total_amount' => 'SUM(amount)']
        )->where(
            'vendor_id = :vendor_id'
        )->where(
            'status = :status'
        );
        $bind = ['vendor_id' => $vendorId,'status'=>WithdrawalCredit::STATUS_PENDING];

        $total = $connection->fetchOne($select, $bind);

        return $total;
    }
}
