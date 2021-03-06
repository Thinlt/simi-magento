<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * WYSIWYG widget options form.
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Vnecoms\PdfPro\Block\Adminhtml\Widget;

class Options extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Element type used by default if configuration is omitted.
     *
     * @var string
     */
    protected $_defaultElementType = 'text';

    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_widget;

    /**
     * @var \Magento\Framework\Option\ArrayPool
     */
    protected $_sourceModelPool;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Framework\Option\ArrayPool     $sourceModelPool
     * @param \Magento\Widget\Model\Widget            $widget
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Option\ArrayPool $sourceModelPool,
        \Magento\Widget\Model\Widget $widget,
        array $data = []
    ) {
        $this->_sourceModelPool = $sourceModelPool;
        $this->_widget = $widget;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Widget Options Form and values according to specified type.
     *
     * The widget_type must be set in data before
     * widget_values may be set before to render element values
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->getForm()->setUseContainer(false);
        $this->addFields();

        return $this;
    }

    /**
     * Form getter/instantiation.
     *
     * @return \Magento\Framework\Data\Form
     */
    public function getForm()
    {
        if ($this->_form instanceof \Magento\Framework\Data\Form) {
            return $this->_form;
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $this->setForm($form);

        return $form;
    }

    /**
     * Fieldset getter/instantiation.
     *
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    public function getMainFieldset()
    {
        if ($this->_getData('main_fieldset') instanceof \Magento\Framework\Data\Form\Element\Fieldset) {
            return $this->_getData('main_fieldset');
        }
        $mainFieldsetHtmlId = 'options_fieldset'.md5($this->getWidgetType());
        $this->setMainFieldsetHtmlId($mainFieldsetHtmlId);
        $fieldset = $this->getForm()->addFieldset(
            $mainFieldsetHtmlId,
            ['legend' => __('Widget Options'), 'class' => 'fieldset-wide']
        );
        $this->setData('main_fieldset', $fieldset);

        // add dependence javascript block
        $block = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $this->setChild('form_after', $block);

        return $fieldset;
    }

    /**
     * Add fields to main fieldset based on specified widget type.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return $this
     */
    public function addFields()
    {
        $form = $this->getForm();
        $data = $this->getWidgetValues();
        $fieldset = $this->getMainFieldset();
        // get configuration node and translation helper
        if (!$this->getWidgetType()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please specify a Widget Type.'));
        }

        foreach (array('header', 'row') as $item) {
            $item == 'header' ? $label = 'Table Header' : $label = 'Item Row';

            $field = $fieldset->addField($item.'_heading', 'text', array(
                'label' => __($label),
            ));
            //set renderer
            $fieldRenderer = $this->getLayout()->createBlock('Vnecoms\PdfPro\Block\Adminhtml\Widget\Form\Renderer\Fieldset\Heading');
            $field->setRenderer($fieldRenderer);

//            $fieldset->addField($item.'_font_family', 'select', array(
//                'label'     => __('Font family'),
//                'class'     => 'required-entry',
//                'required'  => true,
//                'name'      => 'parameters['.$item.'_font_family]',
//                'values'	  => \VnEcoms\AdvancedPdfProcessor\Model\Source\Data::toOptionArray(\VnEcoms\AdvancedPdfProcessor\Model\Source\Data::FONT_FAMILY),
//                'value'	  => isset($data[$item.'_font_family'])?$data[$item.'_font_family']:'0',
//            ));
//            //set renderer
//            $form->getElement($item.'_font_family')->setRenderer(
//                $this->getLayout()->createBlock('\VnEcoms\AdvancedPdfProcessor\Block\Adminhtml\Widget\Form\Renderer\Fieldset\Element')
//            );

            $fieldset->addField($item.'_font_size', 'select', array(
                'label' => __('Font size'),
                'class' => 'required-entry',
                'required' => true,
                'style' => 'min-width:250px;',
                'name' => 'parameters['.$item.'_font_size]',
                'values' => \Vnecoms\PdfPro\Model\Source\Data::toOptionArray(\Vnecoms\PdfPro\Model\Source\Data::FONT_SIZE),
                'value' => isset($data[$item.'_font_size']) ? $data[$item.'_font_size'] : '0',
            ));
            //set renderer
            /*$form->getElement($item.'_font_size')->setRenderer(
                $this->getLayout()->createBlock('\VnEcoms\AdvancedPdfProcessor\Block\Adminhtml\Widget\Form\Renderer\Fieldset\Element')
            );*/

            $fieldset->addField($item.'_font_italic', 'select', array(
                'label' => __('Italic'),
                'name' => 'parameters['.$item.'_font_italic]',
                'style' => 'min-width:250px;',
                'values' => \Vnecoms\PdfPro\Model\Source\Data::toOptionArray(\Vnecoms\PdfPro\Model\Source\Data::FONT_ITALIC),
                'value' => isset($data[$item.'_font_italic']) ? $data[$item.'_font_italic'] : '0',
            ));

            $fieldset->addField($item.'_font_bold', 'select', array(
                'label' => __('Bold'),
                'style' => 'min-width:250px;',
                'name' => 'parameters['.$item.'_font_bold]',
                'values' => \Vnecoms\PdfPro\Model\Source\Data::toOptionArray(\Vnecoms\PdfPro\Model\Source\Data::FONT_BOLD),
                'value' => isset($data[$item.'_font_bold']) ? $data[$item.'_font_bold'] : 0,
            ));
        }
        //for column
        $fieldset->addField('column_heading', 'text', array(
            'label' => __('Columns'),
        ));
        //set renderer
        $form->getElement('column_heading')->setRenderer(
            $this->getLayout()->createBlock('\Vnecoms\PdfPro\Block\Adminhtml\Widget\Form\Renderer\Fieldset\Heading')
        );

        $fieldset->addField('column', 'text', array(
            'name' => 'column',
            'value' => isset($data['column']) ? $data['column'] : '',
            'editor' => $this->getEditor(),
        ));

        $form->getElement('column')->setRenderer(
            $this->getLayout()->createBlock('Vnecoms\PdfPro\Block\Adminhtml\Widget\Form\Renderer\Fieldset\Column')
        );

        return $this;
    }

    /**
     * Add field to Options form based on parameter configuration.
     *
     * @param \Magento\Framework\DataObject $parameter
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _addField($parameter)
    {
        $form = $this->getForm();
        $fieldset = $this->getMainFieldset();
        //$form->getElement('options_fieldset');

        // prepare element data with values (either from request of from default values)
        $fieldName = $parameter->getKey();
        $data = [
            'name' => $form->addSuffixToName($fieldName, 'parameters'),
            'label' => __($parameter->getLabel()),
            'required' => $parameter->getRequired(),
            'class' => 'widget-option',
            'note' => __($parameter->getDescription()),
        ];

        if ($values = $this->getWidgetValues()) {
            $data['value'] = isset($values[$fieldName]) ? $values[$fieldName] : '';
        } else {
            $data['value'] = $parameter->getValue();
            //prepare unique id value
            if ($fieldName == 'unique_id' && $data['value'] == '') {
                $data['value'] = md5(microtime(1));
            }
        }

        // prepare element dropdown values
        if ($values = $parameter->getValues()) {
            // dropdown options are specified in configuration
            $data['values'] = [];
            foreach ($values as $option) {
                $data['values'][] = ['label' => __($option['label']), 'value' => $option['value']];
            }
            // otherwise, a source model is specified
        } elseif ($sourceModel = $parameter->getSourceModel()) {
            $data['values'] = $this->_sourceModelPool->get($sourceModel)->toOptionArray();
        }

        // prepare field type or renderer
        $fieldRenderer = null;
        $fieldType = $parameter->getType();
        // hidden element
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
            // just an element renderer
        } elseif ($fieldType && $this->_isClassName($fieldType)) {
            $fieldRenderer = $this->getLayout()->createBlock($fieldType);
            $fieldType = $this->_defaultElementType;
        }

        // instantiate field and render html
        $field = $fieldset->addField($this->getMainFieldsetHtmlId().'_'.$fieldName, $fieldType, $data);
        if ($fieldRenderer) {
            $field->setRenderer($fieldRenderer);
        }

        // extra html preparations
        if ($helper = $parameter->getHelperBlock()) {
            $helperBlock = $this->getLayout()->createBlock(
                $helper->getType(),
                '',
                ['data' => $helper->getData()]
            );
            if ($helperBlock instanceof \Magento\Framework\DataObject) {
                $helperBlock->setConfig(
                    $helper->getData()
                )->setFieldsetId(
                    $fieldset->getId()
                )->prepareElementHtml(
                    $field
                );
            }
        }

        // dependencies from other fields
        $dependenceBlock = $this->getChildBlock('form_after');
        $dependenceBlock->addFieldMap($field->getId(), $fieldName);
        if ($parameter->getDepends()) {
            foreach ($parameter->getDepends() as $from => $row) {
                $values = isset($row['values']) ? array_values($row['values']) : (string) $row['value'];
                $dependenceBlock->addFieldDependence($fieldName, $from, $values);
            }
        }

        return $field;
    }

    /**
     * Checks whether $fieldType is a class name of custom renderer, and not just a type of input element.
     *
     * @param string $fieldType
     *
     * @return bool
     */
    protected function _isClassName($fieldType)
    {
        return preg_match('/[A-Z]/', $fieldType) > 0;
    }
}
