<?php

namespace Vnecoms\PdfProCustomVariables\Block\Adminhtml\Variables\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 23:29
 */
class Intergration extends Generic implements TabInterface
{

    /** @var \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory  */
    protected $customVariablesFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory $customVariablesFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->customVariablesFactory = $customVariablesFactory;
    }

    /**
     * Prepare html
     *
     * @param void
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->setTemplate('Vnecoms_PdfProCustomVariables::ves_pdfprocustomvariables/intergrationcode.phtml');
        return parent::_beforeToHtml();
    }

    /**
     * @return mixed
     */
    public function getPdfProCustomVariables()
    {
        $variables = $this->_coreRegistry->registry('pdfprocustomvariables_data');
        if (!$variables) {
            return $this->getModel();
        }
        return $variables;
    }

    /**
     * @return \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables
     */
    public function getModel()
    {
        return $this->customVariablesFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Implementation Code');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Implementation Code');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
