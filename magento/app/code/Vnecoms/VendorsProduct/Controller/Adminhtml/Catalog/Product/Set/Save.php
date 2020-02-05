<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Controller\Adminhtml\Catalog\Product\Set;

class Save extends \Vnecoms\VendorsProduct\Controller\Adminhtml\Catalog\Product\Set
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Vnecoms\VendorsProduct\Model\Attribute\SetFactory
     */
    protected $attributeSetFactory;
    
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
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Vnecoms\VendorsProduct\Model\Entity\Attribute\SetFactory $attributeSetFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Retrieve catalog product entity type id
     *
     * @return int
     */
    protected function _getEntityTypeId()
    {
        if ($this->_coreRegistry->registry('entityType') === null) {
            $this->_setTypeId();
        }
        return $this->_coreRegistry->registry('entityType');
    }

    /**
     * Save attribute set action
     *
     * [POST] Create attribute set from another set and redirect to edit page
     * [AJAX] Save attribute set data
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $entityTypeId = $this->_getEntityTypeId();
        $hasError = false;
        $attributeSetId = $this->getRequest()->getParam('id', false);
        
        /* @var $model \Magento\Eav\Model\Entity\Attribute\Set */
        $parentSet = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
            ->setEntityTypeId($entityTypeId);

        /** @var $filterManager \Magento\Framework\Filter\FilterManager */
        $filterManager = $this->_objectManager->get('Magento\Framework\Filter\FilterManager');

        try {
            if ($attributeSetId) {
                $parentSet->load($attributeSetId);
            }
            if (!$parentSet->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('This attribute set no longer exists.')
                );
            }
            $model = $this->attributeSetFactory->create();

            $model->load($attributeSetId, "parent_set_id");

            if (!$model->getId()) {
                $model->setAttributeSetName($parentSet->getAttributeSetName());
                $model->setParentSetId($parentSet->getId());
                $model->save();
                $model->setIsNewObject(true);
            }
            
            
            $data = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')
                ->jsonDecode($this->getRequest()->getPost('data'));
            //filter html tags
            $data['attribute_set_name'] = $filterManager->stripTags($data['attribute_set_name']);

            $model->organizeData($data);

            $model->validate();

            $model->save();
            $this->messageManager->addSuccess(__('You saved the add product form set.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $hasError = true;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the add product form.'));
            $hasError = true;
        }


        $response = [];
        if ($hasError) {
            $layout = $this->layoutFactory->create();
            $layout->initMessages();
            $response['error'] = 1;
            $response['message'] = $layout->getMessagesBlock()->getGroupedHtml();
        } else {
            $response['error'] = 0;
            $response['url'] = $this->getUrl('vendors/*/');
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
}
