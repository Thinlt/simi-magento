<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magecomp\Mobilelogin\Model\LoginotpmodelFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;

class Ajaxverifyotpforlogin extends \Magento\Framework\App\Action\Action
{
    protected $_modelLoginOtpFactory;
    public $_helperdata;
    protected $session;

    public function __construct(
        Context $context,
        LoginotpmodelFactory $modelLoginOtpFactory,
        \Magecomp\Mobilelogin\Helper\Data $helperData,
        Session $customerSession

    )
    {
        $this->_modelLoginOtpFactory = $modelLoginOtpFactory;
        $this->_helperdata = $helperData;
        $this->session = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = "false";
        $mobile = $this->getRequest()->get('mobile');
        $otp = $this->getRequest()->get('otp');
        $isExist = $this->_helperdata->checkLoginOTPCode($mobile, $otp);
        if ($isExist == 1) {
            $customerData = $this->_objectManager->create('\Magento\Customer\Model\Customer');
            $customer = $customerData->getCollection()->addFieldToFilter("mobilenumber", $mobile)->getFirstItem();
            if ($customer) {
                $this->session->setCustomerAsLoggedIn($customer);
                $this->session->regenerateId();
                $data = "true";
                if ($this->_helperdata->isEnableLoginEmail()) {
                    $this->_helperdata->sendMail($_SERVER['REMOTE_ADDR'], $customer->getEmail(), $_SERVER['HTTP_USER_AGENT']);
                }
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }
}