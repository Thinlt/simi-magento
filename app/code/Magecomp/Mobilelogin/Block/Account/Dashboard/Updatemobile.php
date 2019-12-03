<?php
namespace Magecomp\Mobilelogin\Block\Account\Dashboard;

use Magento\Framework\View\Element\Template\Context;

class Updatemobile extends \Magento\Framework\View\Element\Template
{
    protected $_customersession;

    public function __construct(Context $context, \Magento\Customer\Model\Session $customerSession)
    {
        $this->_customersession = $customerSession;
        parent::__construct($context);
    }

    public function getCustomerid()
    {
        $customerId = 0;
        if ($this->_customersession->isLoggedIn()) {
            $customerId = $this->_customersession->getCustomer()->getId();
        }
        return $customerId;
    }

    public function getMobilenumber()
    {
        $mobileNumber = 0;
        if ($this->_customersession->isLoggedIn()) {
            $mobileNumber = $this->_customersession->getCustomer()->getMobilenumber();
        }
        return $mobileNumber;
    }
}