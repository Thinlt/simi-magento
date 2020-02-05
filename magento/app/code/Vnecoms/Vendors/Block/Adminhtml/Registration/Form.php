<?php
/**
 * Catalog price rules
 *
 * @author      Vnecoms Team <core@vnecoms.com>
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Registration;

class Form extends \Vnecoms\Vendors\Block\Adminhtml\Profile\Form
{

    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection $fieldsetCollection,
        \Vnecoms\Vendors\Model\ResourceModel\Attribute\CollectionFactory $attributeCollectionFactory,
        array $data = []
    ) {
        return parent::__construct($context, $registry, $fieldsetCollection, $attributeCollectionFactory);
    }

    /**
     * Init Fieldset collection
     * @see \Vnecoms\Vendors\Block\Adminhtml\Profile\Form::_initCollection()
     */
    protected function _initFieldsetCollection()
    {
        $this->_fieldsetCollection->addFieldToFilter('form', \Vnecoms\Vendors\Helper\Data::REGISTRATION_FORM);
        $this->_fieldsetCollection->setOrder('sort_order', 'ASC');
        return $this;
    }
    
    /**
     * Can use attribute
     * @param \Vnecoms\Vendors\Model\Entity\Attribute $attribute
     * @return boolean
     */
    public function canUseAttribute(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->getData('is_used_in_registration_form');
    }
    
    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __("Manage Seller Registration Form'");
    }
  
    
    /**
     * Get Save Fieldset Url
     * @return string
     */
    public function getSaveFieldsetUrl()
    {
        return $this->getUrl('vendors/form_registration/savefieldset');
    }
    
    /**
     * Get Delete Fieldset Url
     * @return string
     */
    public function getDeleteFieldsetUrl()
    {
        return $this->getUrl('vendors/form_registration/deletefieldset');
    }
    
    /**
     * Get Reload Fieldset Form Url
     * @return string
     */
    public function getReloadFormUrl()
    {
        return $this->getUrl('vendors/form_registration/form');
    }
    
    /**
     * Get Save Fields Order Form Url
     * @return string
     */
    public function getSaveFieldsOrderUrl()
    {
        return $this->getUrl('vendors/form_registration/fieldsorder');
    }
    
    /**
     * Get Save Fieldset Order Url
     * @return string
     */
    public function getSaveFieldsetsOrderUrl()
    {
        return $this->getUrl('vendors/form_registration/fieldsetsorder');
    }
}
