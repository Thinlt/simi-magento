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
        $typeLogin = $data['params']['type'];
        $mobile = $data['params']['mobile'];
        $otp = $data['params']['otp'];
        $isExist = $helperData->checkLoginOTPCode($mobile, $otp);
        $tokenKey = null;
        $customerIdentity = null;
        $redirectUrl = null;
        if ($isExist == 1) {
            $customerData = $this->simiObjectManager->get(\Magento\Customer\Model\Customer::class);
            $customer = $customerData->getCollection()->addFieldToFilter("mobilenumber", $mobile)->getFirstItem();
            if ($customer) {
                if (!$customer->getConfirmation()) {
                    $session = $this->simiObjectManager->get(\Magento\Customer\Model\Session::class);
                    $session->setCustomerAsLoggedIn($customer);
                    $session->regenerateId();
                    $customerIdentity = $this->simiObjectManager->get('\Magento\Customer\Model\Session')->getSessionId();
                    $result = "true";
                    $customerToken = $this->simiObjectManager->get(\Magento\Integration\Model\Oauth\TokenFactory::class);
                    $tokenKey = $customerToken->create()->createCustomerToken($customer->getId())->getToken();
                    if ($helperData->isEnableLoginEmail()) {
                        $helperData->sendMail($_SERVER['REMOTE_ADDR'], $customer->getEmail(), $_SERVER['HTTP_USER_AGENT']);
                    }
                    $helperData->setOtpVerified($mobile);
                    if ($typeLogin === "vendor") {
                        // Check if exist vendor account
                        $vendorCollection = $this->simiObjectManager
                            ->get('Vnecoms\Vendors\Model\Vendor')->getCollection()->addFieldToFilter('telephone', $mobile);
                        if (count($vendorCollection) == 1) {
                            // get redirect url for login vendor
                            $helper = $this->simiObjectManager->get('Vnecoms\Vendors\Helper\Data');
                            $redirectUrl = $helper->getHomePageUrl();
                        } else {
                            $message = __("Designer account does not exist !");
                            return [
                                [
                                    'status' => 'error',
                                    'is_login' => '0',
                                    'message' => $message
                                ]
                            ];
                        }
                    }
                } else {
                    $message = __("This account isn't confirmed. Verify and try again.");
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'message' => $message
                        ]
                    ];
                }
            } else {
                $message = __("Account does not exist !");
                return [
                    [
                        'status' => 'error',
                        'is_login' => '0',
                        'message' => $message
                    ]
                ];
            }
        }
        return [
            'status' => 'success',
            'customer_access_token' => $tokenKey,
            'customer_identity' => $customerIdentity,
            'redirect_url' => $redirectUrl ? ($redirectUrl . '?simiSessId=' . $customerIdentity) : null
        ];
    }
}
