<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Controller\Adminhtml\Dashboard;

use Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock as DefaultAjaxBlock;
use Magento\Framework\Registry;

class AjaxBlock extends DefaultAjaxBlock
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        Registry $coreRegistry
    ) {
        parent::__construct($context, $resultRawFactory, $layoutFactory);
        $this->_coreRegistry = $coreRegistry;
    }
    
    /**
     * Init vendor
     *
     * @return \Vnecoms\VendorsDashboard\Controller\Adminhtml\Dashboard\AjaxBlock
     */
    protected function _initVendor()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Vnecoms\Vendors\Model\Vendor');
        
        $model->load($id);
        
        $this->_coreRegistry->register('current_vendor', $model);
        return $this;
    }
}
