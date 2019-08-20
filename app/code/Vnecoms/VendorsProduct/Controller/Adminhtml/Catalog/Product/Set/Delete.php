<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Controller\Adminhtml\Catalog\Product\Set;

class Delete extends \Vnecoms\VendorsProduct\Controller\Adminhtml\Catalog\Product\Set
{
    /**
     * @var \Vnecoms\VendorsProduct\Model\Entity\Attribute\SetFactory
     */
    protected $setFactory;

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsProduct::catalog_product_form');
    }
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Vnecoms\VendorsProduct\Model\Entity\Attribute\SetFactory $setFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->setFactory = $setFactory;
    }

    
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $setId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $set = $this->setFactory->create();
            $set->load($setId, 'parent_set_id')->delete();
            $this->messageManager->addSuccess(__('The attribute set has been restored.'));
            $resultRedirect->setPath('vendors/*/edit', ['id'=>$setId]);
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t delete this set right now.'));
            $resultRedirect->setUrl($this->_redirect->getRedirectUrl($this->getUrl('*')));
        }
        return $resultRedirect;
    }
}
