<?php
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;

class Info_backup extends Generic implements TabInterface
{
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     *
     * @var \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection
     */
    protected $_fieldsetCollection;
    
    
    /**
     * Region renderer
     * @var \Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Renderer\Region
     */
    protected $_regionRenderer;
    
    /**
     * These attributes will not be showing in the form.
     * @var array
     */
    protected $_excludeAttributes = [];
    
   /**
    *
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\Framework\Data\FormFactory $formFactory
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection $fieldsetCollection,
        \Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Renderer\Region $regionRenderer,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,

                array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_fieldsetCollection = $fieldsetCollection;
        $this->_fieldsetCollection->setOrder('sort_order', 'ASC');
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->_regionRenderer = $regionRenderer;
        
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare content for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Seller Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Seller Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Form
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_vendor');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');
                
        $this->_eventManager->dispatch('ves_vendors_vendor_tab_info_prepare_before', ['tab'=>$this,'form'=>$form]);
        foreach ($this->_fieldsetCollection as $fset) {
            $fieldset = $form->addFieldset('fieldset_'+$fset->getId(), ['legend' => $fset->getTitle()]);
            
            foreach ($fset->getAttributes() as $attribute) {
                if (($inputType = $attribute->getFrontendInput()) && !in_array(
                    $attribute->getAttributeCode(),
                    $this->_excludeAttributes
                ) && ('media_image' != $inputType || $attribute->getAttributeCode() == 'image')
                ) {
                    $fieldType = $inputType;
                    $element = $fieldset->addField(
                        $attribute->getAttributeCode(),
                        $fieldType,
                        [
                            'name' => $attribute->getAttributeCode(),
                            'label' => __($attribute->getFrontendLabel()),
                            'class' => $attribute->getFrontendClass(),
                            'required' => $attribute->getIsRequired(),
                            'note' => $attribute->getNote()
                        ]
                    );
                
//                     $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
                
                     $this->_applyTypeSpecificConfigCustomer($inputType, $element, $attribute);
                }
//                 $fieldset->addField(
//                     $attribute['attribute_code'],
//                     'text',
//                     ['name' => $attribute['attribute_code'], 'label' => $attribute['frontend_label'], 'title' => $attribute['frontend_label'], 'required' => false]
//                 );
            }
            $regionElement = $form->getElement('ves_vendor_region');
            $regionElement->setRequired(true);
            if ($regionElement) {
                $regionElement->setRenderer($this->_regionRenderer);
            }
            
            $regionElement = $form->getElement('ves_vendor_region_id');
            if ($regionElement) {
                $regionElement->setNoDisplay(true);
            }
        }
        
        $this->_eventManager->dispatch('ves_vendors_vendor_tab_info_prepare_after', ['tab'=>$this,'form'=>$form]);
        
        $form->setValues($model->getData());


        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Apply configuration specific for different element type
     *
     * @param string $inputType
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param AttributeMetadataInterface $attribute
     * @return void
     */
    protected function _applyTypeSpecificConfigCustomer(
        $inputType,
        $element,
        \Magento\Eav\Model\Entity\Attribute $attribute
    ) {
        switch ($inputType) {
            case 'select':
                $element->setValues($this->_getAttributeOptionsArray($attribute));
                break;
            case 'multiselect':
                $element->setValues($this->_getAttributeOptionsArray($attribute));
                $element->setCanBeEmpty(true);
                break;
            case 'date':
                $element->setDateFormat($this->_localeDate->getDateFormatWithLongYear());
                break;
            case 'multiline':
                $element->setLineCount($attribute->getMultilineCount());
                break;
            default:
                break;
        }
    }
    
    /**
     * @param AttributeMetadataInterface $attribute
     * @return array
     */
    protected function _getAttributeOptionsArray(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        $options = $attribute->getOptions();
        $result = [];
        foreach ($options as $option) {
            $result[] = [
                'value' => $option->getValue(),
                'label' => $option->getLabel(),
            ];
        }
        return $result;
    }
}
