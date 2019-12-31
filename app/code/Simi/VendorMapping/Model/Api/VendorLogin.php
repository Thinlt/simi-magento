<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\VendorMapping\Model\Api;

use Simi\VendorMapping\Api\VendorLoginInterface;
use Vnecoms\Vendors\Controller\Seller\LoginPost;

class VendorLogin extends LoginPost implements VendorLoginInterface
{
    public function loginPost()
    {

        if ($this->session->isLoggedIn()) {
            $helper = $this->_objectManager->get('Vnecoms\Vendors\Helper\Data');
            $redirectUrl = $helper->getHomePageUrl();
            return [
                [
                    'status' => 'success',
                    'is_login' => '1',
                    'sessionId' => $this->session->getSessionId(),
                    // 'vendor_url' => $redirectUrl,
                    'redirect_url' => $redirectUrl . '?simiSessId=' . $this->session->getSessionId(),
                    // 'redirect_path' => 'vendors/simivendor/vendors',
                ]
            ];
        }

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
                    $customer = $helperCustomer->getCustomerByEmail($login['username']);
                    var_dump($customer->getData()->getId()); die();
                    if (!$customer->getId()) {
                        echo 'co'; die();
                        $message = __("Do not exist account !");
                        return [
                            [
                                'status' => 'error',
                                'is_login' => '0',
                                'message' => $message
                            ]
                        ];
                    } else {
                        echo 'khong'; die();
                        if ($customer->getConfirmation() && $this->isConfirmationRequired($customer)) {
                            $message = __("This account isn't confirmed. Verify and try again.");
                            return [
                                [
                                    'status' => 'error',
                                    'is_login' => '0',
                                    'message' => $message
                                ]
                            ];
                        }
                        $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
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
                    }
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                    $this->session->setUsername($login['username']);
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'redirect_url' => 'account/login',
                            'message' => $message
                        ]
                    ];
                } catch (UserLockedException $e) {
                    $message = __(
                        'The account is locked. Please wait and try again or contact %1.',
                        $this->getScopeConfig()->getValue('contact/email/recipient_email')
                    );
                    $this->session->setUsername($login['username']);
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'redirect_url' => 'account/login',
                            'message' => $message
                        ]
                    ];
                } catch (AuthenticationException $e) {
                    $message = __('Invalid login or wrong password.');
                    $this->session->setUsername($login['username']);
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'redirect_url' => 'account/login',
                            'message' => $message
                        ]
                    ];
                } catch (LocalizedException $e) {
                    $this->session->setUsername($login['username']);
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'redirect_url' => 'account/login',
                            'message' => $message
                        ]
                    ];
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    echo $e->getMessage();
                    die();
                    return [
                        [
                            'status' => 'error',
                            'is_login' => '0',
                            'redirect_url' => 'account/login',
                            'message' => __('An unspecified error occurred. Please contact us for assistance.')
                        ]
                    ];
                }
            } else {
                return [
                    [
                        'status' => 'error',
                        'is_login' => '0',
                        'redirect_url' => 'account/login',
                        'message' => __('A login and a password are required.')
                    ]
                ];
            }
        }

        return [
            [
                'status' => 'error',
                'is_login' => '0',
                'redirect_url' => 'account/login',
                'message' => ''
            ]
        ];
    }
}
