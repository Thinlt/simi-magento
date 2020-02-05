<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Edit\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Attributes extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes
{
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::widget/form.phtml';
    
    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $_productHelper;
    
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Vnecoms\VendorsProduct\Helper\Data $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory);
        $this->_productHelper = $productHelper;
    }
    
    /**
     * Prepare attributes form
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        
        $form = $this->getForm();
        $tierPrice = $form->getElement('tier_price');
        if ($tierPrice) {
            $tierPrice->setRenderer(
                $this->getLayout()->createBlock('Vnecoms\VendorsProduct\Block\Vendors\Product\Edit\Tab\Price\Tier')
            );
        }
        
        return $this;
    }

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = [
            'price' => 'Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form\Price',
            'weight' => 'Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form\Weight',
            'gallery' => 'Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form\Gallery',
            'image' => 'Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form\Image',
            'boolean' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean',
            'textarea' => 'Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg',
        ];

        $response = new \Magento\Framework\DataObject();
        $response->setTypes([]);
        $this->_eventManager->dispatch('adminhtml_catalog_product_edit_element_types', ['response' => $response]);
        
        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
    
    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Vnecoms\Vendors\Block\Vendors\Widget\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Vnecoms\Vendors\Block\Vendors\Widget\Form\Renderer\Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Vnecoms\VendorsProduct\Block\Vendors\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    
        return $this;
    }
    
    
    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     * @return void
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = [])
    {
        $notAllowedAttributes = $this->_productHelper->getNotUsedVendorAttributes();
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            if (in_array($attribute->getAttributeCode(), $notAllowedAttributes)) {
                continue;
            }
            
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            if (!$this->_isAttributeVisible($attribute)) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()->getInputType()) && !in_array(
                $attribute->getAttributeCode(),
                $exclude
            ) && ('media_image' != $inputType || $attribute->getAttributeCode() == 'image')
            ) {
                $fieldType = $inputType;
                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                
                if ($attribute->getAttributeCode() == 'category_ids') {
                    $rendererClass = 'Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form\Category';
                } elseif ($attribute->getAttributeCode() == 'weight') {
                    $rendererClass = 'Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form\Weight';
                } elseif ($attribute->getAttributeCode() == 'image') {
                    $rendererClass = 'Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form\BaseImage';
                }
                
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField(
                    $attribute->getAttributeCode(),
                    $fieldType,
                    [
                        'name' => $attribute->getAttributeCode(),
                        'label' => $attribute->getFrontend()->getLocalizedLabel(),
                        'class' => $attribute->getFrontend()->getClass(),
                        'required' => $attribute->getIsRequired(),
                        'note' => $attribute->getNote()
                    ]
                )->setEntityAttribute(
                    $attribute
                );
    
                    $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
    
                    $this->_applyTypeSpecificConfig($inputType, $element, $attribute);
            }
        }
    }
}
