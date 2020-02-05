<?php

namespace Vnecoms\SmTheme\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class VendorHomePageItems implements ObserverInterface
{    
    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;
    
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
		\Magento\Framework\App\ViewInterface $view,
		JsonFactory $resultJsonFactory
    ) {
        $this->view = $view;
        $this->resultJsonFactory = $resultJsonFactory;
    }
    
    /**
     * Add free gift to shopping cart.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$controller = $observer->getControllerAction();
    	if($controller->getRequest()->getParam('vesAjax')){
	    	$productsBlockHtml = $this->view->getLayout()->getBlock('vendor.products')->setTemplate('Vnecoms_SmTheme::products.phtml')->toHtml();
	    	$leftNav = $this->view->getLayout()->getBlock('vendor.catalog.leftnav');
	    	$controller->getResponse()->setBody(json_encode([
	    			'success' => true,
	    			'html' => [
	    				'products_list' => $productsBlockHtml,
	    				'filters' => $leftNav?$leftNav->toHtml():''
	    			]
	    	]));
    	}
    }
}
