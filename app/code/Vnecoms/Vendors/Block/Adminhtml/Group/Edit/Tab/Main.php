<?php
namespace Vnecoms\Vendors\Block\Adminhtml\Group\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

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
        return __('Group Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Group Information');
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
        $model = $this->_coreRegistry->registry('current_group');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('group_');
        
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Group Information')]);
        
        $this->_eventManager->dispatch('ves_vendors_group_tab_main_prepare_before', ['tab'=>$this,'form'=>$form,'fieldset'=>$fieldset]);
        
        if ($model->getId()) {
            $fieldset->addField('vendor_group_id', 'hidden', ['name' => 'vendor_group_id']);
        }
        
        
        $fieldset->addField(
            'vendor_group_code',
            'text',
            ['name' => 'vendor_group_code', 'label' => __('Group'), 'title' => __('Group'), 'required' => true]
        );
        
        $this->_eventManager->dispatch('ves_vendors_group_tab_main_prepare_after', ['tab'=>$this,'form'=>$form,'fieldset'=>$fieldset]);
        
        $form->setValues($model->getData());


        $this->setForm($form);

        return parent::_prepareForm();
    }
}
