<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\App\Action\Frontend;

use Magento\Framework\Controller\ResultFactory;

/**
 * Backend Controller context
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\Framework\App\Action\Context
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_session;
    
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_helper;
    
    /**
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\App\ViewInterface $view
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Vnecoms\Vendors\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param ResultFactory $resultFactory
     * @param \Vnecoms\Vendors\Model\Session $session
     * @param \Vnecoms\Vendors\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        ResultFactory $resultFactory,
        \Vnecoms\Vendors\Model\Session $session,
        \Vnecoms\Vendors\Helper\Data $helper
    ) {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager,
            $resultRedirectFactory,
            $resultFactory
        );

        $this->_session = $session;
        $this->_helper = $helper;
    }
    
    /**
     * Get vendor session
     *
     * @return \Vnecoms\Vendors\Model\Session
     */
    public function getVendorSession()
    {
        return $this->_session;
    }
    
    /**
     * Get vendor helper
     *
     * @return \Vnecoms\Vendors\Helper\Data
     */
    public function getVendorHelper()
    {
        return $this->_helper;
    }
}
