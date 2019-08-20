<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Controller\Cart;

use Magento\Framework\Exception\NotFoundException;

class CreditPost extends \Magento\Checkout\Controller\Cart
{

    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Vnecoms\Credit\Model\Credit
     */
    protected $creditAccount;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        if($customerId = $customerSession->getId()){
            $this->creditAccount = $creditAccountFactory->create();
            $this->creditAccount->loadByCustomerId($customerId);
        }
        $this->quoteRepository = $quoteRepository;
    }
    
    /**
     * Get max credit that customer can use
     * @return int
     */
    protected function getMaxCredit(){
        $cartQuote = $this->cart->getQuote();
        $address = $cartQuote->isVirtual()?$cartQuote->getBillingAddress():$cartQuote->getShippingAddress();
        $maxAmount = $address->getSubtotal() + $address->getDiscountAmount() + $address->getShippingAmount();
        return $maxAmount;
    }
    
    /**
     * Display customer wishlist
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        if(!$this->creditAccount){
            $this->messageManager->addError(__("You have to login to use this action"));
            return $this->_goBack();
        }
        
        $creditAmount = $this->getRequest()->getParam('remove') == 1?0:$this->getRequest()->getParam('credit_amount');
        if($creditAmount > $this->creditAccount->getCredit()){
            $currency = $this->_storeManager->getStore()->getCurrentCurrency();
            $credit = $this->creditAccount->getCredit();
            $this->messageManager->addError(__("Max credit you can use: %1",$currency->formatPrecision($credit,2,[],false)));
            return $this->_goBack();
        }
        
        $cartQuote = $this->cart->getQuote();
        $itemsCount = $cartQuote->getItemsCount();
        if ($itemsCount) {
            $cartQuote->setBaseCreditAmount(-$creditAmount)->collectTotals();
            $this->quoteRepository->save($cartQuote);
        }
        
        return $this->_goBack();
    }
}
