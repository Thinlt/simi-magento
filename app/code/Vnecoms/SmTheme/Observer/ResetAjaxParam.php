<?php

namespace Vnecoms\SmTheme\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class ResetAjaxParam implements ObserverInterface
{    
    /**
     * Add free gift to shopping cart.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$controller = $observer->getControllerAction();
    	if($controller->getRequest()->getParam('ajax') == 1){
    		$controller->getRequest()->getQuery()->set('ajax', null);
    		$controller->getRequest()->setParam('ajax', null);
    		$requestUri = $controller->getRequest()->getRequestUri();
    		$requestUri = preg_replace('/(\?|&)ajax=1/', '', $requestUri);
    		$controller->getRequest()->setRequestUri($requestUri);
    		$controller->getRequest()->setParam('vesAjax', true);
    	}
    }
}
