<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\Credit\Model\ResourceModel;

/**
 * Cms page mysql resource
 */
class Credit extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_store_credit', 'entity_id');
    }

    /**
     * Get total credit in system.
     * @return float
     */
    public function getTotalCreditInSystem(){
        $table = $this->getTable('ves_store_credit');
        $readCollection = $this->getConnection();
        $sql = "SELECT SUM(credit) as total_credit FROM $table";
        $total = $readCollection->fetchOne($sql);
        return $total;
    }
    
    /**
     * Get number of customer account with credit greater than zero
     * @return int
     */
    public function getNumberCustomerWithCredit(){
        $table = $this->getTable('ves_store_credit');
        $readCollection = $this->getConnection();
        $sql = "SELECT count(entity_id) as num_of_customer FROM $table WHERE credit > 0";
        $total = $readCollection->fetchOne($sql);
        return $total;
    }
}
