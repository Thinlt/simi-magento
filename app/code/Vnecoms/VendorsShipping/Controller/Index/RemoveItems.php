<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Controller\Index;

use Magento\Framework\Exception\NotFoundException;
use Magento\Checkout\Model\Cart as CustomerCart;

class RemoveItems extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;
    
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_vendorConfig;
    
    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Vnecoms\VendorsConfig\Helper\Data $vendorConfig
     * @param CustomerCart $cart
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Vnecoms\VendorsConfig\Helper\Data $vendorConfig,
        CustomerCart $cart
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
        $this->_vendorConfig = $vendorConfig;
        parent::__construct($context);
    }
    
    /**
     * Get Vendor List
     *
     * @param CustomerCart $cart
     */
    public function getVendorList(CustomerCart $cart)
    {
        $vendorsList = [];
        $vendorFactory = $this->_objectManager->create('Vnecoms\Vendors\Model\VendorFactory');
        
        foreach ($cart->getQuote()->getAllVisibleItems() as $quoteItem) {
            $vendorId = $quoteItem->getVendorId();
            if (!$vendorId) {
                continue;
            }
            $vendor = $vendorFactory->create()->load($vendorId);
            if (!$vendor->getId()) {
                continue;
            }
        
            $vendorsList['vendor_'.$vendorId] = $vendor->getData();
            $title = $this->_vendorConfig->getVendorConfig('general/store_information/name', $vendorId);
            $vendorsList['vendor_'.$vendorId]['shipping_title'] = $title?$title:$vendor->getVendorId();
        }
        return $vendorsList;
    }
    
    /**
     * Display customer wishlist
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $vendorId = (int)$this->getRequest()->getParam('vendor_id');
        $response = new \Magento\Framework\DataObject();
        if ($vendorId) {
            try {
                $isRemoved = false;
                foreach ($this->cart->getQuote()->getAllItems() as $item) {
                    if ($item->getProduct()->getVendorId() == $vendorId) {
                        $this->cart->removeItem($item->getId());
                        $isRemoved = true;
                    }
                }
                if ($isRemoved) {
                    $this->cart->save();
                }
                
                $response->setData([
                    'success' => true,
                    'vendors_list' => $this->getVendorList($this->cart),
                ]);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the item.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $response->setData([
                    'success' => false,
                    'vendors_list' => $this->getVendorList($this->cart),
                ]);
            }
        }
        
        return $this->_resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
