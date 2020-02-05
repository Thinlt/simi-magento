<?php
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Tab\Info;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;

class Attributes extends Generic
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
        \Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Renderer\Region $regionRenderer,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,

                array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->_regionRenderer = $regionRenderer;
        
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * @return Form
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_vendor');
        $fset = $this->getVendorFieldset();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');
        
        $fieldset = $form->addFieldset('fieldset_'.$fset->getId(), ['legend' => $fset->getTitle(),'class' => 'fieldset-wide']);
        $fieldset->addType('file', 'Vnecoms\Vendors\Block\Adminhtml\Form\Element\File');
        $fieldset->addType('image', 'Vnecoms\Vendors\Block\Adminhtml\Form\Element\Image');
        $fieldset->addType('boolean', 'Vnecoms\Vendors\Block\Adminhtml\Form\Element\Boolean');
        
        $this->_eventManager->dispatch('ves_vendors_vendor_tab_info_prepare_before', ['tab'=>$this,'form'=>$form,'fieldset'=>$fieldset]);
        
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        
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
                        'name' => 'vendor_data['.$attribute->getAttributeCode().']',
                        'label' => __($attribute->getFrontendLabel()),
                        'class' => $attribute->getAttributeCode() == "vendor_id" ?
                            $attribute->getFrontendClass()." validate-vendor-id" : $attribute->getFrontendClass(),
                        'required' => $attribute->getIsRequired(),
                        'note' => $attribute->getNote()
                    ]
                );
                
                if ($attribute->getFrontendModel()) {
                    //echo $attribute->getFrontendModel();exit;
                    //$element->setRenderer($om->create($attribute->getFrontendModel()));
                }
                
                $this->_applyTypeSpecificConfigCustomer($inputType, $element, $attribute);
            }
        }
        
        /*Update region field*/
        $regionElement = $form->getElement('region');
        if ($regionElement) {
            $regionElement->setRequired(true);
            $regionElement->setRenderer($this->_regionRenderer);
        }
        
        $regionElement = $form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }

        $this->_eventManager->dispatch('ves_vendors_vendor_tab_info_prepare_after', ['tab'=>$this,'form'=>$form,'fieldset'=>$fieldset]);
        
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
