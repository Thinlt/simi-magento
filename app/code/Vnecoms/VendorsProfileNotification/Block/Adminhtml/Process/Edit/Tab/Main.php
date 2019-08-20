<?php
namespace Vnecoms\VendorsProfileNotification\Block\Adminhtml\Process\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Vnecoms\VendorsProfileNotification\Helper\Data
     */
    protected $helper;
    
   /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\Framework\Data\FormFactory $formFactory
    * @param \Vnecoms\VendorsProfileNotification\Helper\Data $helper
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Vnecoms\VendorsProfileNotification\Helper\Data $helper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->helper = $helper;
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
        return __('Process Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Process Information');
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
        $model = $this->coreRegistry->registry('current_process');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('process_');
        
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Group Information')]);
        
        
        if ($model->getId()) {
            $fieldset->addField('process_id', 'hidden', ['name' => 'process_id']);
        }
        
        
        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Title'), 'title' => __('Title'), 'required' => true]
        );
        
        $types = $this->helper->getType();
        $typeOptions = [];
        foreach($types as $key=>$type){
            $typeOptions[$key] = $type->getTitle();
        }
        $fieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label' => __('Type'),
                'title' => __('Type'),
                'required' => true,
                'options' => $typeOptions
            ]
        );
        
        $fieldset->addField(
            'sort_order',
            'text',
            ['name' => 'sort_order', 'label' => __('Sort Order'), 'title' => __('Sort Order'), 'required' => false, 'class' => 'validate-number']
        );
        
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'options' => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ]
            ]
        );
        
        foreach($types as $type){
            $type->prepareForm($form, $model);
        }
        
        if(!$model->getId()){
            $model->setStatus(\Vnecoms\VendorsProfileNotification\Model\Process::STATUS_ENABLED);
        }
        
        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
