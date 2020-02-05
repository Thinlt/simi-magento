<?php

/**
 * Copyright © 2019 Simi. All rights reserved.
 */

namespace Simi\Simicustomize\Model;

use Hybrid_Auth;

class Customer extends \Simi\Simiconnector\Model\Customer
{

    public function login($data)
    {
        return $this->simiObjectManager->get('Simi\Simicustomize\Override\Helper\Customer')
            ->loginByEmailAndPass($data['params']['email'], $data['params']['password']);
    }

    /*
     * Social Login (post method)
     * @param 
     * $data - Object with at least:
     * $data['contents']->email
     * $data['contents']->password
     * $data['contents']->firstname
     * $data['contents']->lastname
     * $data['contents']->telephone
     */

    public function socialLogin($data)
    {
        $data = (object) $data['contents'];
        if (!$data->email) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Cannot Get Your Email. Please let your application provide an email to login.'), 4);
        }
        $customer = $this->simiObjectManager
            ->get('Simi\Simicustomize\Override\Helper\Customer')->getCustomerByEmail($data->email);

        if ($customer->getId()) {
            // If exist account with that email, check confirmation
            if ($customer->getConfirmation()) {
                throw new \Simi\Simiconnector\Helper\SimiException(__('This account is not confirmed. Verify and try again.'), 4);
            }
            //Check authenticate with facebook, google or twitter
            // Only twitter need accessTokenSecret
            if (isset($data->providerId) && isset($data->accessToken)) {
                switch ($data->providerId) {
                    case "facebook.com":
                        try {
                            $config = [
                                'callback'  => \Hybridauth\HttpClient\Util::getCurrentUrl(),
                                'keys' => ['id' => '2565225196864428', 'secret' => 'f59fdb06ce2456ebe4a456421479246c'],
                                'endpoints' => [
                                    'api_base_url'     => 'https://graph.facebook.com/v2.12/',
                                    'authorize_url'    => 'https://www.facebook.com/dialog/oauth',
                                    'access_token_url' => 'https://graph.facebook.com/oauth/access_token',
                                ]
                            ];
                            $adapter = new \Hybridauth\Provider\Facebook($config);

                            $adapter->setAccessToken(['access_token' => $data->accessToken]);

                            $userProfile = $adapter->getUserProfile();

                            $adapter->disconnect();
                        } catch (\Exception $e) {
                            throw new \Simi\Simiconnector\Helper\SimiException(__($e->getMessage()), 4);
                        }
                        break;
                    case "google.com":
                        try {
                            $config = [
                                'callback'  => \Hybridauth\HttpClient\Util::getCurrentUrl(),
                                'keys' => ['id' => '772735916871-qmp7locp0nn7tjrr6qmhcgp98rj84kbn.apps.googleusercontent.com', 'secret' => 'uopFGQe8_tGXkFScVw321QiG']
                            ];

                            $adapter = new \Hybridauth\Provider\Google($config);

                            $adapter->setAccessToken(['access_token' => $data->accessToken]);

                            $userProfile = $adapter->getUserProfile();

                            $adapter->disconnect();
                        } catch (\Exception $e) {
                            throw new \Simi\Simiconnector\Helper\SimiException(__($e->getMessage()), 4);
                        }
                        break;
                    case "twitter.com":
                        try {
                            // $currentUrl = \Hybridauth\HttpClient\Util::getCurrentUrl();
                            $config = [
                                'callback'  => \Hybridauth\HttpClient\Util::getCurrentUrl(),
                                'keys' => ['key' => 'inE1PMSfzSbJZFjzar2pruHC9', 'secret' => 'EqZ7rrFcnGmdAfovg2NEyCNBXxunRSaXcjpxesinnrVEguqS2l'],
                                'authorize' => true
                            ];

                            $adapter = new \Hybridauth\Provider\Twitter($config);
                            if ($data->accessTokenSecret) {
                                $adapter->setAccessToken([
                                    'access_token' => $data->accessToken,
                                    'access_token_secret' => $data->accessTokenSecret
                                ]);
                            } else {
                                throw new \Simi\Simiconnector\Helper\SimiException(__('Twitter need access token secret !'), 4);
                            }

                            $userProfile = $adapter->getUserProfile();

                            $adapter->disconnect();
                        } catch (\Exception $e) {
                            throw new \Simi\Simiconnector\Helper\SimiException(__($e->getMessage()), 4);
                        }
                        break;
                }

                // Check if exist response from facebook, google or twitter
                if ($userProfile && $userProfile->identifier) {
                    // Check if above identifier the same as the identifier returned by pwa studio
                    if ($userProfile->identifier === $data->userSocialId) {
                        // If the same -> force login ( need return 2 fields: customer_access_token and customer_identity)

                        // Login by customer object, this function only create new customer session id ( customer_identity)
                        $this->simiObjectManager
                            ->get('Simi\Simicustomize\Override\Helper\Customer')->loginByCustomer($customer);
                        // Create new customer access token ( customer_access_token )
                        $tokenModel = $this->simiObjectManager->create('\Magento\Integration\Model\Oauth\Token');
                        $tokenModel->createCustomerToken($customer->getId());
                    } else {
                        // Not the same, show error
                        throw new \Simi\Simiconnector\Helper\SimiException(__('Your account is Invalid !'), 4);
                    }
                } else {
                    throw new \Simi\Simiconnector\Helper\SimiException(__('Your account is not authenticated by ' . $data->providerId . ' !'), 4);
                }
            } else {
                throw new \Simi\Simiconnector\Helper\SimiException(__('Invalid login !'), 4);
            }
        } else {
            if (!$data->firstname) {
                $data->firstname = __('Firstname');
            }
            if (!$data->lastname) {
                $data->lastname = __('Lastname');
            }
            // Create new customer account for social network
            $customer = $this->_createCustomer($data);
            // Notify user to check mailbox and verify new account
            throw new \Simi\Simiconnector\Helper\SimiException(__('Please check your mailbox to active your account !.'), 4);
        }
    }
}
