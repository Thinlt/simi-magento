<?php
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor\Create\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Model\AccountManagement;

class Main extends Generic implements TabInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    
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
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
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
        return __('Personal Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Personal Information');
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
     * Prepare form before rendering HTML
     *
     */
    protected function _prepareForm()
    {
        //var_dump($model->getData());exit;
        /** @var \Magento\Framework\Data\Form $form */

        $model = $this->_coreRegistry->registry('current_customer');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Personal Information')]);
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $website = $om->get('Vnecoms\Vendors\Model\Source\Website');
        $fieldset->addField(
            'website_id',
            'select',
            [
                'name' => 'website_id',
                'label' => __("Associate to Website"),
                'title' => __('Associate to Website'),
                'required' => true,
                'options' => $website->getOptionArray()
            ]
        );
        /*
        $store = $om->get('Vnecoms\Vendors\Model\Source\Store');
        $fieldset->addField(
            'sendemail_store_id',
            'select',
            [
                'name' => 'sendemail_store_id',
                'label' => __("Send Welcome Email From"),
                'title' => __('Send Welcome Email From'),
                'required' => true,
                'options' => $store->getOptionArray()
            ]
        );*/

        $fieldset->addField(
            'firstname',
            'text',
            [
                'name' => 'firstname',
                'label' => __('First name'),
                'title' => __('First name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'lastname',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Last name'),
                'title' => __('Last name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'class' => 'validate-email'
            ]
        );

        $fieldset->addType('vendor_password', 'Vnecoms\Vendors\Block\Adminhtml\Form\Element\Password');
        $password = $fieldset->addField(
            'password',
            'vendor_password',
            [
                'name' => 'password',
                'label' => __('Password'),
                'id' => 'password',
                'title' => __('Email'),
                'class' => 'input-text validate-password validate-customer-password required-entry',
                'required' => true,
                'data-password-min-length' => $this->getMinimumPasswordLength(),
                'data-password-min-character-sets' => $this->getRequiredCharacterClassesNumber()
            ]
        );

        $fieldset->addField(
            'confirmation',
            'vendor_password',
            [
                'name' => 'password_confirmation',
                'label' => __('Confirm Password'),
                'id' => 'confirmation',
                'title' => __('Confirm Password'),
                'class' => 'input-text validate-cpassword-customer required-entry',
                'required' => true,
            ]
        );


        $this->_eventManager->dispatch('ves_vendors_vendor_tab_main_prepare_after',array('tab'=>$this,'form'=>$form,'fieldset'=>$fieldset));
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }


    /**
     * Get minimum password length
     *
     * @return string
     * @since 100.1.0
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get number of password required character classes
     *
     * @return string
     * @since 100.1.0
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }
}
