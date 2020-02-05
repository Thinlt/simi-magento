<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Account\Create;

use Vnecoms\Vendors\Model\Session as VendorSession;
use Vnecoms\Vendors\Model\Source\RegisterType;

class Vendor extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection
     */
    protected $_fieldsetCollection;
    
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;
    
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * @var array
     */
    protected $_fieldsets;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection $fieldsetCollection,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        VendorSession $vendorSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_fieldsetCollection = $fieldsetCollection;
        $this->_vendorHelper = $vendorHelper;
        $this->_vendorSession = $vendorSession;
        $this->_coreRegistry = $coreRegistry;
        $this->_fieldsetCollection->addFieldToFilter('form', \Vnecoms\Vendors\Helper\Data::REGISTRATION_FORM);
        $this->_fieldsetCollection->setOrder("sort_order", 'ASC');
        parent::__construct($context, $data);
    }
    
    /**
     * Get fieldset collection
     * @return \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection
     */
    public function getFieldsetCollection()
    {
        return $this->_fieldsetCollection;
    }
    
    /**
     * Get fieldset blocks
     * @return array:
     */
    public function getFieldsetBlocks()
    {
        if (!$this->_fieldsets) {
            $this->_fieldsets = [];
            foreach ($this->_fieldsetCollection as $fieldset) {
                $block = $this->getLayout()->createBlock('Vnecoms\Vendors\Block\Account\Create\Fieldset')
                    ->setFieldset($fieldset)
                    ->setTemplate('account/create/fieldset.phtml');
                $this->_fieldsets[] = $block;
            }
        }
        return $this->_fieldsets;
    }
    
    
    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
        if (!$this->getData('form_data')) {
            if ($parentBlock = $this->getLayout()->getBlock('customer_form_register')) {
                $this->setFormData($parentBlock->getFormData());
            } else {
                $this->setFormData($this->_coreRegistry->registry('form_data'));
            }
        }
        return $this->getData('form_data');
    }
    
    /**
     * Is open seller account
     * @return boolean
     */
    public function isOpenSellerAccount()
    {
        $formData = $this->getFormData();
        if (!isset($formData['is_seller']) || !$formData['is_seller']) {
            return false;
        }
        return true;
    }
    
    /**
     * Is Enabled Agreement
     *
     * @return int
     */
    public function isEnableAgreement()
    {
        return $this->_vendorHelper->isEnabledRegistrationAgreement();
    }
    
    /**
     * Get Agreement Label
     *
     * @return string;
     */
    public function getAgreementLabel()
    {
        return $this->_vendorHelper->getAgreementLabel();
    }
    
    /**
     * @see \Magento\Framework\View\Element\Template::_toHtml()
     */
    protected function _toHtml()
    {
        if (!$this->_vendorHelper->moduleEnabled() ||
            !$this->_vendorHelper->isEnableVendorRegister()
        ) {
            return '';
        }
    
        if ($this->_vendorHelper->getSellerRegisterType() == RegisterType::TYPE_SEPARATED &&
            $this->getData('register_form_type') == 'customer_seller'
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
