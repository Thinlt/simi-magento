<?php
namespace Vnecoms\Vendors\Block\Vendors\Account\Edit\Form;

use Vnecoms\Vendors\Block\Vendors\Widget\Form;
use Vnecoms\Vendors\Block\Vendors\Widget\Form\Generic;
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
     * @var \Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer\Region
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
        \Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer\Region $regionRenderer,
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
        
        $fieldset = $form->addFieldset('fieldset_'.$fset->getId(), []);
        $fieldset->addType('file', 'Vnecoms\Vendors\Block\Vendors\Widget\Form\Element\File');
        $fieldset->addType('image', 'Vnecoms\Vendors\Block\Vendors\Widget\Form\Element\Image');
        $fieldset->addType('boolean', 'Vnecoms\Vendors\Block\Vendors\Widget\Form\Element\Boolean');
        
        $this->_eventManager->dispatch('ves_vendors_vendor_tab_info_prepare_before', ['tab'=>$this,'form'=>$form,'fieldset'=>$fieldset]);
        
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        
        foreach ($fset->getAttributes() as $attribute) {
            if (($inputType = $attribute->getFrontendInput()) && !in_array(
                $attribute->getAttributeCode(),
                $this->_excludeAttributes
            ) && ('media_image' != $inputType || $attribute->getAttributeCode() == 'image')
                && ! $attribute->getHideFromVendorPanel()
                && $attribute->getIsUsedInProfileForm()
            ) {
                $fieldType = $inputType;
                $element = $fieldset->addField(
                    $attribute->getAttributeCode(),
                    $fieldType,
                    [
                        'name' => 'vendor_data['.$attribute->getAttributeCode().']',
                        'label' => __($attribute->getFrontendLabel()),
                        'class' => $attribute->getFrontendClass(),
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

        foreach ($this->getCustomRenderers() as $fieldId => $rendererType) {
            $element = $form->getElement($fieldId);
            if (!$element) {
                continue;
            }
            $renderer = $this->getLayout()->createBlock($rendererType);
            $element->setRenderer($renderer);
        }
        $this->_eventManager->dispatch('ves_vendors_vendor_tab_info_prepare_after', ['tab'=>$this,'form'=>$form,'fieldset'=>$fieldset]);
        
        $form->setValues($model->getData());


        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Get Custom Renderers
     * @return multitype:string
     */
    protected function getCustomRenderers()
    {
        return [
            'vendor_id' => 'Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer\Vendor',
            'group_id' => 'Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer\Group',
            'status' => 'Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer\Status',
        ];
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
