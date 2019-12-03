<?php
namespace Magecomp\Mobilelogin\Model;
class Forgototpmodel extends \Magento\Framework\Model\AbstractModel
{

    const CACHE_TAG = 'sms_forgot_otp';

    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magecomp\Mobilelogin\Model\ResourceModel\Forgototpmodel');
    }
}
