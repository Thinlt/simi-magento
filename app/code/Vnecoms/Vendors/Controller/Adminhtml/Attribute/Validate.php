<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Adminhtml\Attribute;

class Validate extends \Vnecoms\Vendors\Controller\Adminhtml\Attribute
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
    }

    
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_attributes');
    }
    
    
    public function execute()
    {
        $result = new \Magento\Framework\DataObject();
        $result->setError(false);
        $request = $this->getRequest();

        $attrCode = $request->getParam('attribute_code');
        $frontendLabelData = $request->getParam('frontend_label');
        $attrCode = $attrCode ?: $this->generateCode($frontendLabelData[0]);
        $attrId = $request->getParam('attribute_id');
        $attribute = $this->_objectManager->create(
            'Vnecoms\Vendors\Model\Entity\Attribute'
        )->loadByCode(
            $this->_entityTypeId,
            $attrCode
        );

        if ($attribute->getId() && !$attrId) {
            if (strlen($request->getParam('attribute_code'))) {
                $result->setMessage(
                    __('An attribute with this code already exists.')
                );
            } else {
                $result->setMessage(
                    __('An attribute with the same code (%1) already exists.', $attrCode)
                );
            }
            $result->setError(true);
        }
        
        return $this->resultJsonFactory->create()->setJsonData($result->toJson());
    }
}
