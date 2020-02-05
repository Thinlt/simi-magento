<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simi\Simicustomize\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\Token as Token;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Event\ManagerInterface;

/**
 * @inheritdoc
 */
class CustomerTokenService extends \Magento\Integration\Model\CustomerTokenService
{
    /**
     * Token Model
     *
     * @var TokenModelFactory
     */
    protected $tokenModelFactory;

    /**
     * @var Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Customer Account Service
     *
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Integration\Model\CredentialsValidator
     */
    protected $validatorHelper;

    /**
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    protected $tokenModelCollectionFactory;

    /**
     * @var RequestThrottler
     */
    protected $requestThrottler;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param AccountManagementInterface $accountManagement
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     * @param \Magento\Integration\Model\CredentialsValidator $validatorHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        AccountManagementInterface $accountManagement,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper,
        ManagerInterface $eventManager = null
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->accountManagement = $accountManagement;
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
        $this->validatorHelper = $validatorHelper;
        $this->eventManager = $eventManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ManagerInterface::class);
    }

    /**
     * Get customer_access_token for login pwa studio
     *
     * The function will return the token from the oauth_token table.
     *
     * @param int $customerId
     * @return string
     * @throws \Simi\Simiconnector\Helper\SimiException
     */
    public function getCustomerAccessToken($customerId)
    {
        $tokenCollection = $this->tokenModelCollectionFactory->create()->addFilterByCustomerId($customerId);
        if ($tokenCollection->getSize() == 0) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('You must login by general account first !'), 4);
        }
        try {
            $listToken = $tokenCollection->getData();
            $size = sizeof($listToken);
            // get lastest auth_token
            $token = $listToken[$size - 1]['token'];
        } catch (\Exception $e) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Get token fail.'), 4);
        }
        return $token;
    }
}
