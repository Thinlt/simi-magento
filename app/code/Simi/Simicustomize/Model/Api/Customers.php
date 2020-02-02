<?php

/**
 * Copyright © 2019 Simi. All rights reserved.
 */

namespace Simi\Simicustomize\Model\Api;

class Customers extends \Simi\Simiconnector\Model\Api\Customers
{
    /**
     * Override this function to implement sociallogin
     */
    public function setBuilderQuery()
    {
        $data = $this->getData();
        if ($data['resourceid']) {
            switch ($data['resourceid']) {
                case 'forgetpassword':
                    $this->simiObjectManager->get('Simi\Simiconnector\Model\Customer')->forgetPassword($data);
                    $email                 = $data['params']['email'];
                    $this->builderQuery    = $this->simiObjectManager
                        ->create('Magento\Customer\Model\Session')->getCustomer();
                    $this->RETURN_MESSAGE = $message = __(
                        'If there is an account associated with %1 you will '
                            . 'receive an email with a link to reset your password.',
                        $email
                    );
                    break;
                case 'createpassword':
                    $data = (object) $data['contents'];
                    if (!isset($data->password))
                        throw new \Simi\Simiconnector\Helper\SimiException(__('Missing new password'), 4);
                    if (!isset($data->rptoken))
                        throw new \Simi\Simiconnector\Helper\SimiException(__('Missing reset password token'), 4);
                    $newPW = $data->password;
                    $resetPasswordToken = $data->rptoken;
                    // Check rp_token in database exist or not
                    $customerData = $this->simiObjectManager->create('\Magento\Customer\Model\Customer');
                    $customerSearch = $customerData->getCollection()->addFieldToFilter("rp_token", $resetPasswordToken);
                    if (count($customerSearch) == 0) {
                        throw new \Simi\Simiconnector\Helper\SimiException(__('Token expired or invalid !'), 4);
                    }
                    // If exist, create new password
                    $this->simiObjectManager
                        ->get('Magento\Customer\Model\Session')
                        ->setRpToken($resetPasswordToken);
                    $this->createPassword($newPW, $resetPasswordToken);
                    $this->simiObjectManager
                        ->get('Magento\Customer\Model\Session')
                        ->setRpToken(null);
                    $this->builderQuery    = $this->simiObjectManager
                        ->get('Magento\Customer\Model\Session')->getCustomer();
                    $this->RETURN_MESSAGE = $message = __('You updated your password.');
                    break;
                case 'profile':
                    $this->builderQuery    = $this->simiObjectManager
                        ->get('Magento\Customer\Model\Session')->getCustomer();
                    $this->builderQuery->setData('wishlist_count', $this->getWishlistCount());
                    break;
                case 'login':
                    if ($this->simiObjectManager->get('Simi\Simicustomize\Model\Customer')->login($data)) {
                        $this->builderQuery = $this->simiObjectManager
                            ->get('Magento\Customer\Model\Session')->getCustomer();
                        $this->builderQuery->setData('wishlist_count', $this->getWishlistCount());
                    } else {
                        throw new \Simi\Simiconnector\Helper\SimiException(__('Login Failed'), 4);
                    }
                    break;

                case 'sociallogin':
                    $this->builderQuery = $this->simiObjectManager->get('Simi\Simicustomize\Model\Customer')
                        ->socialLogin($data);
                    $this->builderQuery = $this->simiObjectManager
                        ->get('Magento\Customer\Model\Session')->getCustomer();
                    $this->builderQuery->setData('wishlist_count', $this->getWishlistCount());
                    break;

                case 'logout':
                    $lastCustomerId     = $this->simiObjectManager->get('Magento\Customer\Model\Session')
                        ->getCustomer()->getId();
                    if ($this->simiObjectManager->get('Simi\Simiconnector\Model\Customer')->logout()) {
                        $this->builderQuery = $this->simiObjectManager
                            ->get('Magento\Customer\Model\Customer')->load($lastCustomerId);
                    } else {
                        throw new \Simi\Simiconnector\Helper\SimiException(__('Logout Failed'), 4);
                    }
                    break;
                case 'checkexisting':
                    $this->builderQuery = $this->simiObjectManager->get('Simi\Simiconnector\Model\Customer')
                        ->getCustomerByEmail($data['params']['customer_email']);
                    break;
                default:
                    $this->builderQuery = $this->simiObjectManager->get('Magento\Customer\Model\Customer')
                        ->setWebsiteId($this->storeManager->getStore()->getWebsiteId())->load($data['resourceid']);
                    break;
            }
        } else {
            $currentCustomerId  = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getId();
            $this->builderQuery = $this->simiObjectManager->get('Magento\Customer\Model\Customer')->getCollection()
                ->addFieldToFilter('entity_id', $currentCustomerId);
        }
    }

    /*
     * Add Message
     */

    public function getDetail($info)
    {
        $data = $this->getData();
        if ($data['resourceid'] !== 'sociallogin') {
            $resultArray            = parent::getDetail($info);
            if ($this->RETURN_MESSAGE)
                $resultArray['message'] = [$this->RETURN_MESSAGE];

            if (isset($resultArray['customer']) && isset($resultArray['customer']['email'])) {
                if (
                    $this->simiObjectManager->get('\Magento\Newsletter\Model\Subscriber') &&
                    $this->simiObjectManager->get('\Magento\Newsletter\Model\Subscriber')
                    ->loadByEmail($resultArray['customer']['email'])->isSubscribed()
                ) {
                    $resultArray['customer']['news_letter'] = '1';
                } else {
                    $resultArray['customer']['news_letter'] = '0';
                }
                $hash = $this->simiObjectManager
                    ->get('Simi\Simiconnector\Helper\Customer')
                    ->getToken($data);
                $resultArray['customer']['simi_hash'] = $hash;
            }

            return $resultArray;
        } else {
            if (isset($info['email'])) {
                $resultArray            = parent::getDetail($info);
                if (
                    $this->simiObjectManager->get('\Magento\Newsletter\Model\Subscriber') &&
                    $this->simiObjectManager->get('\Magento\Newsletter\Model\Subscriber')
                    ->loadByEmail($info['email'])->isSubscribed()
                ) {
                    $info['news_letter'] = '1';
                } else {
                    $info['news_letter'] = '0';
                }
                $hash = $this->simiObjectManager
                    ->get('Simi\Simiconnector\Helper\Customer')
                    ->getToken($data);
                $info['simi_hash'] = $hash;
                // First: get customer access token
                $tokenService = $this->simiObjectManager->create('Simi\Simicustomize\Model\CustomerTokenService');
                $customerAccessToken = $tokenService->getCustomerAccessToken($resultArray['customer']['entity_id']);

                // Second: get customer_identity
                $customerIdentity = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getSessionId();

                return ([
                    'customer_access_token' => $customerAccessToken,
                    'customer_identity' => $customerIdentity
                ]);
            }
        }
    }
}
