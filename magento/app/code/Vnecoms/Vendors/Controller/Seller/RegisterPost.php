<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Seller;

use Vnecoms\Vendors\Model\Session as VendorSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Action\Context;
use Vnecoms\Vendors\Model\Vendor;

class RegisterPost extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
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
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        VendorSession $vendorSession,
        ScopeConfigInterface $scopeConfig,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
    ) {
        $this->_vendorSession = $vendorSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_vendorHelper = $vendorHelper;
        $this->_vendorFactory = $vendorFactory;
        $this->_messageManager = $context->getMessageManager();

        parent::__construct($context);
    }

    /**
     * Renders CMS Home page
     *
     * @param string|null $coreRoute
     * @return \Magento\Framework\Controller\Result\Forward
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($coreRoute = null)
    {
        if (!$this->_vendorHelper->moduleEnabled()) {
            return $this->_forward('no-route');
        }
        
        if (!$this->_vendorHelper->isEnableVendorRegister()) {
            $this->_messageManager->addError(__("You don't have permission to access this page"));
            return $this->_redirect('customer/account');
        }
        
        $vendorData = $this->getRequest()->getParam('vendor_data');
        
        if ($vendorData && is_array($vendorData)) {
            try {
                $vendor = $this->_vendorFactory->create();
                $vendor->setData($vendorData);
                $vendor->setGroupId($this->_vendorHelper->getDefaultVendorGroup());
            
                $customer = $this->_vendorSession->getCustomer();
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
                    return $this->_redirect('vendors/account/login',['success_message' => base64_encode(__("Your seller account has been created. You can now login to vendor panel."))]);
                }
                $this->_messageManager->addSuccess($message);
                return $this->_redirect('vendors');
            } catch (\Exception $e) {
                $this->_messageManager->addError($e->getMessage());
                $this->_vendorSession->setFormData($vendorData);
                return $this->_redirect('marketplace/seller/register');
            }
        } else {
            $this->_messageManager->addError(__("POST data is invalid"));
            return $this->_redirect('marketplace/seller/register');
        }
    }
}
