<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Ui\Component\Template\Form\Element;

use Magento\Ui\Component\Form\Element\AbstractElement;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Registry;
use Vnecoms\PdfPro\Helper\Data as AdvancedHelper;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Vnecoms\PdfPro\Block\Adminhtml\Form\Element\Chooser as ChooserElement;

/**
 * Class Input.
 */
class Chooser extends AbstractElement
{
    const NAME = 'chooser';

    /**
     * @var AdvancedHelper
     */
    protected $_helper;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    protected $keyFactory;

    protected $_categoryFactory;

    /**
     * @var \VnEcoms\AdvancedPdfProcessor\Model\VariableFactory
     */
    protected $_variableFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var ChooserElement
     */
    protected $chooser;

    protected $layout;
    /**
     * Get component name.
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    public function __construct(
        ContextInterface $context,
        \Magento\Backend\Model\UrlInterface $urlInterface,
        \Vnecoms\PdfPro\Model\VariableFactory $variableFactory,
        \Vnecoms\PdfPro\Model\CategoryFactory $categoryFactory,
        \Vnecoms\PdfPro\Model\TemplateFactory $templateFactory,
        AdvancedHelper $helper,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Framework\View\LayoutInterface $layoutInterface,
        array $components = [],
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->_backendUrl = $urlInterface;
        $this->templateFactory = $templateFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_variableFactory = $variableFactory;
        $this->_helper = $helper;
        $this->layout = $layoutInterface;

        $this->form = $formFactory->create();
        $chooseTemplateRenderer = $this->layout->createBlock(
            'Vnecoms\PdfPro\Block\Adminhtml\Helper\Chooser'
        );

        $this->chooser = $this->form->addField(
            $context->getNamespace().'_'.$data['name'],
            'Magento\Framework\Data\Form\Element\Text',
            [
                'name' => $data['name'],
            ]
        )
        ->setRenderer($chooseTemplateRenderer)
        ;
        $data['config']['content'] = $this->chooser->getElementHtml();

        parent::__construct($context, $components, $data);
    }
}
