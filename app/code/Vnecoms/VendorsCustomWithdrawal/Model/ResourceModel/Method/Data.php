<?php
namespace Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method;

class Data extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_withdrawal_method_data', 'data_id');
    }
}
