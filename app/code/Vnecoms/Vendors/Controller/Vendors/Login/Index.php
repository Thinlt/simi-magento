<?php

namespace Vnecoms\Vendors\Controller\Vendors\Login;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_session;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Vnecoms\Vendors\Model\UrlInterface
     */
    protected $backendUrl;
    
    /**
     * @param Context $context
     * @param \Vnecoms\Vendors\Model\Session $session
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Vnecoms\Vendors\Model\UrlInterface $backendUrl
     */
    public function __construct(
        Context $context,
        \Vnecoms\Vendors\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Vnecoms\Vendors\Model\UrlInterface $backendUrl
    ) {
        $this->_session = $session;
        $this->resultPageFactory = $resultPageFactory;
        $this->backendUrl = $backendUrl;
        
        return parent::__construct($context);
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        if($this->_session->isLoggedIn()){
            return $this->_redirectUrl($this->backendUrl->getStartupPageUrl());
        }
        if($message = $this->getRequest()->getParam('success_message')){
            $this->messageManager->addSuccess(base64_decode($message));
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Seller Login'));
        return $resultPage;;
    }
    
    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        return $this->getResponse();
    }
    
}
