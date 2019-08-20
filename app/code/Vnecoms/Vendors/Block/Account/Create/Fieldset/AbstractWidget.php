<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Vendors\Block\Account\Create\Fieldset;

class AbstractWidget extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Vnecoms\Vendors\Model\Attribute
     */
    protected $_attribute;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param CustomerMetadataInterface $customerMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        // default template location
        $this->setTemplate('account/create/fieldset/text.phtml');
    }
    
    /**
     * Set vendor attribute
     * @param \Vnecoms\Vendors\Model\Attribute $attribute
     * @return \Vnecoms\Vendors\Block\Account\Create\Fieldset\AbstractWidget
     */
    public function setVendorAttribute(\Vnecoms\Vendors\Model\Attribute $attribute)
    {
        $this->_attribute = $attribute;
        $frontendInput = $this->_attribute->getFrontendInput();
        if ($frontendInput == 'select') {
            $this->setTemplate('account/create/fieldset/select.phtml');
        } elseif ($frontendInput == 'multiselect') {
            $this->setTemplate('account/create/fieldset/multiselect.phtml');
        } elseif ($frontendInput == 'file') {
            $this->setTemplate('account/create/fieldset/file.phtml');
        } elseif ($frontendInput == 'hidden') {
            $this->setTemplate('account/create/fieldset/hidden.phtml');
        }
        return $this;
    }
    
    /**
     * Get vendor attribute
     * @return \Vnecoms\Vendors\Model\Attribute
     */
    public function getVendorAttribute()
    {
        return $this->_attribute;
    }
    
    /**
     * @return string
     */
    public function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', 'vendor_%s');
        }
        return $this->getData('field_id_format');
    }

    /**
     * @return string
     */
    public function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', 'vendor_data[%s]');
        }
        return $this->getData('field_name_format');
    }

    /**
     * @return string
     */
    public function getFieldId()
    {
        return sprintf($this->getFieldIdFormat(), $this->getAttributeCode());
    }

    /**
     * @param string $field
     * @return string
     */
    public function getFieldName()
    {
        return sprintf($this->getFieldNameFormat(), $this->getAttributeCode());
    }
    
    /**
     * Get attribute code
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->_attribute->getAttributeCode();
    }

    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
        if ($parentBlock = $this->getLayout()->getBlock('customer_form_register')) {
            return $parentBlock->getFormData();
        }elseif ($parentBlock = $this->getLayout()->getBlock('vendor.create')) {
            return $parentBlock->getFormData();
        }elseif ($dataForm = $this->_coreRegistry->registry('form_data')) {
            return ['vendor' => $dataForm];
        }
        return [];
    }
    
    /**
     * @param string $field
     */
    public function getVendorData($field = false)
    {
        $formData = $this->getFormData();
        $vendorData = isset($formData['vendor_data'])?$formData['vendor_data']:[];
        if ($field) {
            return isset($vendorData[$field])?$vendorData[$field]:'';
        }
        return $vendorData;
    }
    
    /**
     * Get field value
     * @return string
     */
    public function getFieldValue()
    {
        return $this->getVendorData($this->getAttributeCode());
    }
}
