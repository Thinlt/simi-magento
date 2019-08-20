<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Vendors\Model\Vendor;
use Magento\Customer\Model\Session;

class CustomerRegisterSuccess implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;
    
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $_vendorFactory;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    
    /**
     * @var Session
     */
    protected $customerSession;
    
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $vendorHelper;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\Vendors\Helper\Data $vendorHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
     * @param Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        Session $customerSession
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_vendorHelper = $vendorHelper;
        $this->_vendorFactory = $vendorFactory;
        $this->_messageManager = $messageManager;
        $this->customerSession = $customerSession;
    }
    
    /**
     * Add the notification if there are any vendor awaiting for approval.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_vendorHelper->isEnableVendorRegister()) {
            return;
        }
        
        $customer = $observer->getCustomer();
        $controller = $observer->getAccountController();
        $vendorData = $controller->getRequest()->getParam('vendor_data');
        
        if (!$controller->getRequest()->getParam('is_seller', false)) {
            return;
        }
        
        if ($vendorData && is_array($vendorData)) {
            $vendor = $this->_vendorFactory->create();
            $vendor->setData($vendorData);
            $vendor->setGroupId($this->_vendorHelper->getDefaultVendorGroup());
            $vendor->setCustomer($customer);
            $vendor->setWebsiteId($customer->getWebsiteId());
            
            if ($this->_vendorHelper->isRequiredVendorApproval()) {
                $vendor->setStatus(Vendor::STATUS_PENDING);
                $message = __("Your seller account has been created and awaiting for approval.");
            } else {
                $vendor->setStatus(Vendor::STATUS_APPROVED);
                $message = __("Your seller account has been created.");
            }
            
            $errors = $vendor->validate();
            
            if ($errors !== true) {
                throw new \Exception(implode("<br />", $errors));
            }
            
            $vendor->save();
            
            if($this->_vendorHelper->isUsedCustomVendorUrl()){
                $redirectUrl = $this->_vendorHelper->getUrl('account/login', ['success_message' => base64_encode(__("Your seller account has been created. You can now login to vendor panel."))]);
            }else{
                $redirectUrl = $this->_vendorHelper->getHomePageUrl();
                $this->_messageManager->addSuccess($message);
            }

            $this->customerSession->setBeforeAuthUrl($redirectUrl);

            if ($this->_vendorHelper->isRequiredVendorApproval()) {
                $vendor->sendNewAccountEmail("registered");
            } else {
                $vendor->sendNewAccountEmail("active");
            }
        }
    }
}
