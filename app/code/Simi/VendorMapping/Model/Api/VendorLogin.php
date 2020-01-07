<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\VendorMapping\Model\Api;

use Simi\VendorMapping\Api\VendorLoginInterface;
use Vnecoms\Vendors\Controller\Seller\LoginPost;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;

class VendorLogin extends LoginPost implements VendorLoginInterface
{

    public function loginPost()
    {

        // if ($this->session->isLoggedIn()) {
        //     $helper = $this->_objectManager->get('Vnecoms\Vendors\Helper\Data');
        //     $redirectUrl = $helper->getHomePageUrl();
        //     return [
        //         [
        //             'status' => 'success',
        //             'is_login' => '1',
        //             'sessionId' => $this->session->getSessionId(),
        //             // 'vendor_url' => $redirectUrl,
        //             'redirect_url' => $redirectUrl . '?simiSessId=' . $this->session->getSessionId()
        //             // 'redirect_path' => 'vendors/simivendor/vendors',
        //         ]
        //     ];
        // }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost();
            if (!isset($login['username'])) {
                try {
                    $requestBody = $this->getRequest()->getContent();
                    $login = json_decode($requestBody, true);
                } catch (\Exception $e) {
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'message' => __('Request body not a json string.')
                        ]
                    ];
                }
            }

            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    // get customer by username (email)
                    $simiObjectManager = $this->_objectManager;
                    $helperCustomer = $simiObjectManager->create('Simi\Simicustomize\Override\Helper\Customer');
                    // type of customer is Magento\Customer\Model\Customer
                    $customer = $helperCustomer->getCustomerByEmail($login['username']);
                    $idCustomer = $customer->getData('entity_id');
                    if (!$idCustomer) {
                        $message = __("Does not exist account !");
                        return [
                            [
                                'status' => 'error',
                                'is_login' => '0',
                                'message' => $message
                            ]
                        ];
                    }

                    if ($customer->getConfirmation() && $customer->isConfirmationRequired()) {
                        $message = __("This account isn't confirmed. Verify and try again.");
                        return [
                            [
                                'status' => 'error',
                                'is_login' => '0',
                                'message' => $message
                            ]
                        ];
                    }

                    $accountManagement = $simiObjectManager->create('Simi\Simicustomize\Model\AccountManagement');

                    if ($accountManagement->getAuthentication()->isLocked(intval($idCustomer))) {
                        $message = __('The account is locked.');
                        return [
                            [
                                'status' => 'error',
                                'is_login' => '0',
                                'message' => $message
                            ]
                        ];
                    }

                    try {
                        $accountManagement->getAuthentication()->authenticate($idCustomer, $login['password']);
                    } catch (InvalidEmailOrPasswordException $e) {
                        $message = __('Your password is wrong !');
                        return [
                            [
                                'status' => 'error',
                                'is_login' => '0',
                                'message' => $message
                            ]
                        ];
                    }
                    $customerRepository = $simiObjectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
                    // type of customer is Magento\Customer\Model\Data\Customer
                    $customerData = $customerRepository->get($login['username']);
                    // $customer = $accountManagement->authenticate($login['username'], $login['password']);
                    $customer = $accountManagement->updateCustomer($customerData, $login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                        $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                        $metadata->setPath('/');
                        $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                    }
                    $helper = $this->_objectManager->get('Vnecoms\Vendors\Helper\Data');
                    $redirectUrl = $helper->getHomePageUrl();
                    // URL is checked to be internal in $this->_redirect->success()
                    return [
                        [
                            'status' => 'success',
                            'is_login' => '1',
                            'sessionId' => $this->session->getSessionId(),
                            // 'vendor_url' => $redirectUrl,
                            'redirect_url' => $redirectUrl . '?simiSessId=' . $this->session->getSessionId(),
                            // 'redirect_path' => 'vendors/simivendor/vendors',
                            'message' => __('Login is successful.'),
                        ]
                    ];
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    echo $e->getMessage();
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'message' => __('An unspecified error occurred. Please contact us for assistance.')
                        ]
                    ];
                }
            } else {
                return [
                    [
                        'status' => 'error',
                        'is_login' => '0',
                        'message' => __('A login and a password are required.')
                    ]
                ];
            }
        }

        return [
            [
                'status' => 'error',
                'is_login' => '0',
                'message' => ''
            ]
        ];
    }
}
