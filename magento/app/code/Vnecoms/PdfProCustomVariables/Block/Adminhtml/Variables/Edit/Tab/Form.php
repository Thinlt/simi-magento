<?php
namespace Vnecoms\PdfProCustomVariables\Block\Adminhtml\Variables\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables;

class Form extends Generic implements TabInterface
{


    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory  */
    protected $attCollectionFactory;

    /** @var \Magento\Catalog\Model\Product\AttributeSet\Options  */
    protected $attributeSetOptions;

    /** @var \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory  */
    protected $customerAttrCollectionFactory;

    /** @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory  */
    protected $fieldFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory $productFactory,
     */
    protected $productFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $customerAttrCollectionFactory
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options $attributeSetOptions
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $customerAttrCollectionFactory,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeSetOptions,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->fieldFactory = $fieldFactory;
        $this->attCollectionFactory = $attCollectionFactory;
        $this->attributeSetOptions = $attributeSetOptions;
        $this->customerAttrCollectionFactory = $customerAttrCollectionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Item Information');
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

    /**
     * Prepare form before rendering HTML
     *
     * @return Generic
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_variable');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('variable_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getCustomVariableId() != null) {
            $fieldset->addField('custom_variable_id', 'hidden', ['name' => 'custom_variable_id']);
        }

        $disabled   = $this->_coreRegistry->registry('current_variable')->getId()?true:false;

        // Name
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'class' => 'required-entry validate-data',
                'label' => __('Variable Name'),
                'title' => __('Variable Name'),
                'required' => true
            ]
        );

        // Variable Type

        $fieldset->addField(
            'variable_type',
            'select',
            [
                'name' => 'variable_type',
                'class' => '',
                'label' => __('Type'),
                'title' => __('Type'),
                'required' => true,
                'disabled'  => $disabled,
                'options' => [
                        PdfproCustomVariables::VARIABLE_TYPE_PRODUCT => __('Product Attribute'),
                        PdfproCustomVariables::VARIABLE_TYPE_CUSTOMER => __('Customer Attribute'),
                        //'static' => __('Static Attribute')
                ]
            ]
        );

        $productsAttributes = [];
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection  $productAttCollection */
        $productAttCollection = $this->attCollectionFactory->create();
        $entityTypeId = $this->productFactory->create()->getResource()->getTypeId();
        $productAttCollection->setEntityTypeFilter($entityTypeId);
        foreach ($productAttCollection as $attribute) {
            $productsAttributes[$attribute->getAttributeId()] = $attribute->getFrontendLabel();
        }
        //var_dump($productsAttributes);die;

        /*attribute id*/
        $fieldset->addField(
            'attribute_id',
            'select',
            [
                'label'     => __('Attributes'),
                'required'  => true,
                'name'      => 'attribute_id',
                'options'    => $productsAttributes
            ]
        );

        $attributes_customer = [];
        /** @var \Magento\Customer\Model\ResourceModel\Attribute\Collection $attCollection */
        $attCollection = $this->customerAttrCollectionFactory->create()->getData();
        //var_dump($attCollection);die;
        foreach ($attCollection as $atts) {
            $attributes_customer[$atts['attribute_id']] = $atts['frontend_label'];
        }

        $fieldset->addField(
            'attribute_id_customer',
            'select',
            [
                'label'     => __('Attributes'),
                'required'  => true,
                'name'      => 'attribute_id_customer',
                'options'    => $attributes_customer
            ]
        );

        /*static value hidden*/
        $fieldset->addField(
            'static_value',
            'text',
            [
                'label'     => __('Static Value'),
                'class' => '',
                'required'  => false,
                'name'      => 'static_value',
            ]
        );

        $form->setValues($model->getData());


        $this->setForm($form);

        if ($this->_getSession()->getPdfProCustomVariablesData()) {
            $form->setValues($this->_getSession()->getPdfProCustomVariablesData());
            $this->_getSession()->setPdfProCustomVariablesData(null);
        } elseif ($this->_coreRegistry->registry('pdfprocustomvariables_data')) {
            $form->setValues($this->_coreRegistry->registry('pdfprocustomvariables_data'));
        }

        $this->_eventManager->dispatch('adminhtml_variables_edit_tab_form_prepare_form', ['form' => $form]);

        return parent::_prepareForm();
    }

    /**
     * Processing block html after rendering
     *
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /**
         * Form template has possibility to render child block 'form_after', but we can't use it because parent
         * form creates appropriate child block and uses this alias. In this case we can't use the same alias
         * without core logic changes, that's why the code below was moved inside method '_afterToHtml'.
         */
        /** @var $formAfterBlock \Magento\Backend\Block\Widget\Form\Element\Dependence */
        $formAfterBlock = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence',
            'adminhtml.block.widget.form.element.dependence'
        );
        $formAfterBlock->addFieldMap(
            $htmlIdPrefix . 'variable_type',
            'variable_type'
        )->addFieldMap(
            $htmlIdPrefix . 'attribute_id',
            'attribute_id'
        )->addFieldDependence(
            'attribute_id',
            'variable_type',
            'attribute'
        );

        $formAfterBlock->addFieldMap(
            $htmlIdPrefix . 'variable_type',
            'variable_type'
        )->addFieldMap(
            $htmlIdPrefix . 'attribute_id_customer',
            'attribute_id_customer'
        )->addFieldDependence(
            'attribute_id_customer',
            'variable_type',
            'customer'
        );
        $html = $html . $formAfterBlock->toHtml();

        return $html;
    }

    /**
     * @return \Magento\Framework\Session\SessionManagerInterface
     */
    protected function _getSession()
    {
        return $this->_session;
    }
}
