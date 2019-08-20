<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CreditConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Vnecoms\Credit\Model\Credit
     */
    protected $creditAccount;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     * @var \Vnecoms\Credit\Helper\Data
     */
    protected $_creditHelper;
    
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Vnecoms\Credit\Helper\Data $creditHelper,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory
    ) {
        $this->_creditHelper = $creditHelper;
        $this->_customerSession = $customerSession;
        if($customerId = $customerSession->getId()){
            $this->creditAccount = $creditAccountFactory->create();
            $this->creditAccount->loadByCustomerId($customerId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output = [];
        $groupId = $this->_customerSession->getCustomerGroupId();
        $output['canUseCredit'] = $this->_creditHelper->canUseCredit($groupId);
        if($this->creditAccount){
            $output['storeCredit'] =[
                'balance' => $this->creditAccount->getCredit()
            ];
        }
        return $output;
    }
}
