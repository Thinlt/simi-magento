<?php
/**
 * Catalog price rules
 *
 * @author      Vnecoms Team <core@vnecoms.com>
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Profile;

class Form extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Fieldset collection
     * @var \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection
     */
    protected $_fieldsetCollection;
    
    /**
     * Vendor attribute collection
     * @var \Vnecoms\Vendors\Model\ResourceModel\Attribute\Collection
     */
    protected $_attributeCollection;
    
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
        $this->_coreRegistry = $registry;
        $this->_fieldsetCollection = $fieldsetCollection;
        $this->_attributeCollection = $attributeCollectionFactory->create();
        $this->_attributeCollection->addVisibleFilter();
        
        $this->_initFieldsetCollection();
        parent::__construct($context, $data);
    }

    /**
     * Init Fieldset collection
     * @return \Vnecoms\Vendors\Block\Adminhtml\Profile\Form
     */
    protected function _initFieldsetCollection()
    {
        $this->_fieldsetCollection->addFieldToFilter('form', \Vnecoms\Vendors\Helper\Data::PROFILE_FORM);
        $this->_fieldsetCollection->setOrder('sort_order', 'ASC');
        return $this;
    }
    
    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = '';
        $this->_controller = '';
        
        parent::_construct();
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('save');
        //$this->updateButton('save', 'label', __('Save Changes'));
        
        $this->addButton(
            'add_fieldset',
            [
                'label' => __('Add Fieldset'),
                'onclick' => 'vesAddFieldset()',
                'class' => 'add primary'
            ],
            -1
        );
        return $this;
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __("Manage Seller Profile Form'");
    }
    
    /**
     * Can use attribute
     * @param \Vnecoms\Vendors\Model\Entity\Attribute $attribute
     * @return boolean
     */
    public function canUseAttribute(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->getData('is_used_in_profile_form');
    }
    
    /**
     * Get Fieldset Collection
     * @return \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection
     */
    public function getFieldsetCollection()
    {
        return $this->_fieldsetCollection;
    }
    
    /**
     * Get vendor attribute collection.
     * @return \Magento\Customer\Model\ResourceModel\Attribute\Collection
     */
    public function getVendorAttributeCollection()
    {
        return $this->_attributeCollection;
    }
    
    /**
     * Get all attributes that will not be showing in vendor form.
     */
    public function getExcludedAttributes()
    {
        return [
            'disable_auto_group_change',
            'is_vendor',
            'website_id'
        ];
    }
    
    /**
     * Get fieldsets JSON
     * @return string
     */
    public function getFieldsetsJSON()
    {
        $data = [];
        foreach ($this->_fieldsetCollection as $fieldset) {
            $data[$fieldset->getId()] = $fieldset->getData();
        }
        return json_encode($data);
    }
    
    /**
     * Get Save Fieldset Url
     * @return string
     */
    public function getSaveFieldsetUrl()
    {
        return $this->getUrl('vendors/form_profile/savefieldset');
    }
    
    /**
     * Get Delete Fieldset Url
     * @return string
     */
    public function getDeleteFieldsetUrl()
    {
        return $this->getUrl('vendors/form_profile/deletefieldset');
    }
    
    /**
     * Get Reload Fieldset Form Url
     * @return string
     */
    public function getReloadFormUrl()
    {
        return $this->getUrl('vendors/form_profile/form');
    }
    
    /**
     * Get Save Fields Order Form Url
     * @return string
     */
    public function getSaveFieldsOrderUrl()
    {
        return $this->getUrl('vendors/form_profile/fieldsorder');
    }
    
    /**
     * Get Save Fieldset Order Url
     * @return string
     */
    public function getSaveFieldsetsOrderUrl()
    {
        return $this->getUrl('vendors/form_profile/fieldsetsorder');
    }
}
