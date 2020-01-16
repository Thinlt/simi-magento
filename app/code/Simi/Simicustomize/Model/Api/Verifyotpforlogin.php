<?php

namespace Simi\Simicustomize\Model\Api;

class Verifyotpforlogin extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function setBuilderQuery()
    {
        // TODO: Implement setBuilderQuery() method.
    }
    public function index()
    {
        $result = "false";
        $data = $this->getData();
        $helperData = $this->simiObjectManager->get(\Magecomp\Mobilelogin\Helper\Data::class);
        $mobile = $data['params']['mobile'];
        $otp = $data['params']['otp'];
        $isExist = $helperData->checkLoginOTPCode($mobile, $otp);
        $tokenKey = null;
        if ($isExist == 1) {
            $customerData = $this->simiObjectManager->get(\Magento\Customer\Model\Customer::class);
            $customer = $customerData->getCollection()->addFieldToFilter("mobilenumber", $mobile)->getFirstItem();
            if ($customer) {
                $session = $this->simiObjectManager->get(\Magento\Customer\Model\Session::class);
                $session->setCustomerAsLoggedIn($customer);
                $session->regenerateId();
                $result = "true";
                $customerToken = $this->simiObjectManager->get(\Magento\Integration\Model\Oauth\TokenFactory::class);
                $tokenKey = $customerToken->create()->createCustomerToken($customer->getId())->getToken();
                if ($helperData->isEnableLoginEmail()) {
                    $helperData->sendMail($_SERVER['REMOTE_ADDR'], $customer->getEmail(), $_SERVER['HTTP_USER_AGENT']);
                }
            }
        }
        return [
            'result' => $result,
            'customer_access_token' => $tokenKey
        ];
    }
}
