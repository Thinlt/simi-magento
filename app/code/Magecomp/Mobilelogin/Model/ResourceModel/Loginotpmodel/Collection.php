<?php
namespace Magecomp\Mobilelogin\Model\ResourceModel\Loginotpmodel;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magecomp\Mobilelogin\Model\Loginotpmodel', 'Magecomp\Mobilelogin\Model\ResourceModel\Loginotpmodel');
    }
}
