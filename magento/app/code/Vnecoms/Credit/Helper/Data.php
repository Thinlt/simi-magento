<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_PATH_BUY_CREDIT_PAGE_TITLE = 'credit/buy_credit_page/page_title';
    const XML_PATH_BUY_CREDIT_PAGE_KEYWORDS = 'credit/buy_credit_page/meta_keyword';
    const XML_PATH_BUY_CREDIT_PAGE_DESCRIPTION = 'credit/buy_credit_page/meta_description';
    
    const XML_PATH_GENERAL_DISPLAY_MYCREDIT_TO_LINKS = 'credit/general/display_credit_top_links';
    const XML_PATH_GENERAL_CREDIT_GROUP = 'credit/general/credit_group';
    const XML_PATH_EMAIL_SENDER = 'credit/email/sender_email_identity';
    const XML_PATH_CREDIT_BALANCE_CHANGE_EMAIL = 'credit/email/credit_balance_change';

    
    /**
     * @var \Vnecoms\Credit\Helper\Email
     */
    protected $_emailHelper;
    
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Vnecoms\Credit\Helper\Email $emailHelper
    ) {
        parent::__construct($context);
        $this->_emailHelper = $emailHelper;
    }
    /**
     * Get title of buy credit page
     * @return string
     */
    public function getBuyCreditPageTitle(){
        return $this->scopeConfig->getValue(
            self::XML_PATH_BUY_CREDIT_PAGE_TITLE
        );
    }
    
    /**
     * Get meta keywords of buy credit page
     * @return string
     */
    public function getBuyCreditPageKeywords(){
        return $this->scopeConfig->getValue(
            self::XML_PATH_BUY_CREDIT_PAGE_KEYWORDS
        );
    }
    
    /**
     * Get meta description of buy credit page
     * @return string
     */
    public function getBuyCreditPageDescription(){
        return $this->scopeConfig->getValue(
            self::XML_PATH_BUY_CREDIT_PAGE_DESCRIPTION
        );
    }
    
    /**
     * Is display My Credit on top links
     * 
     * @return boolean
     */
    public function isDisplayMyCreditOnTopLinks(){
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_DISPLAY_MYCREDIT_TO_LINKS
        );
    }
    
    /**
     * Can use credit
     * 
     * @param int $customerGroupId
     * @return boolean
     */
    public function canUseCredit($customerGroupId){
        if(!$customerGroupId) return false;
        
        $allowedGroups = $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_CREDIT_GROUP
        );
        $allowedGroups = explode(",", $allowedGroups);

        return in_array($customerGroupId, $allowedGroups);
    }
    
    /**
     * Send credit balance change notification email
     * @param \Vnecoms\Credit\Model\Credit $creditAccount
     * @param \Vnecoms\Credit\Model\Credit\Transaction $transaction
     */
    public function sendCreditBalanceChangeEmail(
        \Vnecoms\Credit\Model\Credit $creditAccount,
        \Vnecoms\Credit\Model\Credit\Transaction $transaction
    ) {
        $customer = $creditAccount->getCustomer();
        $this->_emailHelper->sendTransactionEmail(
            self::XML_PATH_CREDIT_BALANCE_CHANGE_EMAIL,
            \Magento\Framework\App\Area::AREA_FRONTEND,
            self::XML_PATH_EMAIL_SENDER,
            $customer->getEmail(),
            [
                'credit_account' => $creditAccount,
                'transaction' => $transaction,
                'customer' => $customer
            ]
        );
    }
}
