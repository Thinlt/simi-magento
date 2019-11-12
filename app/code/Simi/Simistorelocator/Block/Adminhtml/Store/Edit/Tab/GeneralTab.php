<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab;

class GeneralTab extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $simiObjectManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm() {
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('store_');

        /*
         * General Field Set
         */
        $fieldset = $form->addFieldset(
                'general_fieldset', [
            'legend' => __('General Information'),
            'collapsable' => true,
                ]
        );
        $data = $model->getData();
        if ($model->getId()) {
            $fieldset->addField('simistorelocator_id', 'hidden', ['name' => 'simistorelocator_id']);

            $simiconnectorhelper = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Data');
            $typeID              = $simiconnectorhelper->getVisibilityTypeId('storelocator');
            $visibleStoreViews   = $this->simiObjectManager
                ->create('Simi\Simiconnector\Model\Visibility')->getCollection()
                ->addFieldToFilter('content_type', $typeID)
                ->addFieldToFilter('item_id', $model->getId());
            $storeIdArray        = [];

            foreach ($visibleStoreViews as $visibilityItem) {
                $storeIdArray[] = $visibilityItem->getData('store_view_id');
            }

            $data['storeview_id'] = implode(',', $storeIdArray);
        }

        $storeResourceModel = $this->simiObjectManager
            ->create('Simi\Simiconnector\Model\ResourceModel\Storeviewmultiselect');

        $fieldset->addField('storeview_id', 'multiselect', [
            'name'     => 'storeview_id[]',
            'label'    => __('Store View'),
            'title'    => __('Store View'),
            'required' => true,
            'values'   => $storeResourceModel->toOptionArray(),
        ]);

        $fieldset->addField(
                'store_name', 'text', [
            'name' => 'store_name',
            'label' => __('Store Name'),
            'title' => __('Store Name'),
            'required' => true,
                ]
        );

        $fieldset->addField(
                'description', 'editor', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'wysiwyg' => true,
                ]
        );

        $fieldset->addField(
                'status', 'select', [
            'label' => __('Status'),
            'title' => __('Status'),
            'name' => 'status',
            'options' => \Simi\Simistorelocator\Model\Status::getAvailableStatuses(),
                ]
        );

        $fieldset->addField(
                'link', 'text', [
            'name' => 'link',
            'label' => __('Store\'s Link'),
            'title' => __('Store\'s Link'),
                ]
        );

        $fieldset->addField(
                'sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
                ]
        );

        /*
         * Contact Field Set
         */
        $fieldset = $form->addFieldset(
                'contact_fieldset', [
            'legend' => __('Contact Information'),
            'collapsable' => true,
                ]
        );

        $fieldset->addField(
                'phone', 'text', [
            'name' => 'phone',
            'label' => __('Phone Number'),
            'title' => __('Phone Number'),
                ]
        );

        $fieldset->addField(
                'email', 'text', [
            'name' => 'email',
            'label' => __('Email Address'),
            'title' => __('Email Address'),
                ]
        );

        $fieldset->addField(
                'fax', 'text', [
            'name' => 'fax',
            'label' => __('Fax Number'),
            'title' => __('Fax Number'),
                ]
        );

        /*
         * Meta Information Field Set
         */
        $fieldset = $form->addFieldset(
                'meta_fieldset', [
            'legend' => __('Meta Information'),
            'collapsable' => true,
                ]
        );

        $fieldset->addField(
                'rewrite_request_path', 'text', [
            'name' => 'rewrite_request_path',
            'label' => __('URL Key'),
            'title' => __('URL Key'),
            'class' => 'validate-identifier'
                ]
        );

        $fieldset->addField(
                'meta_title', 'text', [
            'name' => 'meta_title',
            'label' => __('Meta Title'),
            'title' => __('Meta Title'),
                ]
        );

        $fieldset->addField(
                'meta_keywords', 'textarea', [
            'name' => 'meta_keywords',
            'label' => __('Meta Keywords'),
            'title' => __('Meta Keywords'),
                ]
        );
        $fieldset->addField(
                'meta_description', 'textarea', [
            'name' => 'meta_description',
            'label' => __('Meta Description'),
            'title' => __('Meta Description'),
                ]
        );

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * get registry model.
     *
     * @return \Simi\Simistorelocator\Model\Store
     */
    public function getRegistryModel() {
        return $this->_coreRegistry->registry('simistorelocator_store');
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel() {
        return __('General information');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle() {
        return __('General information');
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     *
     * @api
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     *
     * @api
     */
    public function isHidden() {
        return false;
    }
}
