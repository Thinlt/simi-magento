<?php
namespace Magecomp\Mobilelogin\Model\ResourceModel;
class Regotpmodel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);     
    }
    
    protected function _construct()
    {
        $this->_init('sms_register_otp', 'id');
    }
}
