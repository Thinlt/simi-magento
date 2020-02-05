<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\Adminhtml\System\Variable;

/**
 * Class WysiwygPlugin.
 */
class WysiwygPlugin extends \Magento\Variable\Controller\Adminhtml\System\Variable
{
    /**
     * @var \Vnecoms\PdfPro\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Vnecoms\PdfPro\Model\VariableFactory
     */
    protected $_variableFactory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_pdfHelper;

    /**
     * WysiwygPlugin constructor.
     *
     * @param \Vnecoms\PdfPro\Model\CategoryFactory             $categoryFactory
     * @param \Vnecoms\PdfPro\Model\VariableFactory             $variableFactory
     * @param \Vnecoms\PdfPro\Helper\Data                       $pdfHelper
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\Registry                       $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\View\LayoutFactory             $layoutFactory
     */
    public function __construct(
        \Vnecoms\PdfPro\Model\CategoryFactory $categoryFactory,
        \Vnecoms\PdfPro\Model\VariableFactory $variableFactory,
        \Vnecoms\PdfPro\Helper\Data $pdfHelper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory,
            $resultJsonFactory, $resultPageFactory, $layoutFactory);
        $this->_categoryFactory = $categoryFactory;
        $this->_variableFactory = $variableFactory;
        $this->_pdfHelper = $pdfHelper;
    }

    /**
     * WYSIWYG Plugin Action.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $pdfVaribles = array();

        $categories = $this->_categoryFactory->create()->getCollection()
            ->addFieldToFilter('type_variable', $this->_pdfHelper->isTypeVariable())->setOrder('sort_order', 'asc');
        foreach ($categories as $category) {
            $categoryData = ['label' => $category->getTitle()];
            $variables = $this->_variableFactory->create()->getCollection()->addFieldToFilter('category_id', $category->getId())->setOrder('sort_order', 'asc');
            foreach ($variables as $variable) {
                $categoryData['value'][] = [
                    'label' => __($variable->getTitle()),
                    'value' => $variable->getCode(),
                ];
            }

            $pdfVaribles[] = $categoryData;
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($pdfVaribles);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Variable::variable');
    }
}
