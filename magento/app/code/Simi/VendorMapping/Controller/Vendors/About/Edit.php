<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\About;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class Edit extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    const XML_PATH_STORE_ABOUT = 'general/store/about';

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::store_about';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context
    ) {
        $this->objectManager = $context->getObjectManager();
        $this->dataPersistor = $this->objectManager->get(\Magento\Framework\App\Request\DataPersistorInterface::class);
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // $om = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $this->objectManager->get(\Vnecoms\VendorsConfig\Helper\Data::class);
        $vendor = $this->_vendorsession->getVendor();
        if ($this->getRequest()->getParam('id') != $vendor->getId()) {
            if ($this->getRequest()->getParam('redirected')) {
                $this->_redirect('*/*');
                return;
            }
            $this->_redirect('*/*/edit/id/'.$vendor->getId().'/redirected/1');
            return;
        }
        $content = $helper->getVendorConfig(self::XML_PATH_STORE_ABOUT, $vendor->getId());
        $this->dataPersistor->set('vendor_about', array(
            'id' => $vendor->getId(),
            'content' => $content
        ));

        $this->_initAction();
        $this->setActiveMenu($this->_aclResource);
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $title->prepend(__("About Store"));
        $this->_addBreadcrumb(__("About Store"), __("About Store"));
        $this->_view->renderLayout();
    }
}
