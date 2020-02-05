<?php

namespace Vnecoms\VendorsProfileNotification\Model\ResourceModel;

class Process extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_profile_notification', 'process_id');
    }
}
