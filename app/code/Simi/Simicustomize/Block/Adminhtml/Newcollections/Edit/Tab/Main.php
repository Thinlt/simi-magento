<?php

namespace Simi\Simicustomize\Block\Adminhtml\Newcollections\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    public $simiObjectManager;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;

    /**
     * @var \Simi\Simicustomize\Helper\Website
     * */
    public $websiteHelper;

    /**
     * @var \Simi\Simicustomize\Model\Newcollections
     */
    public $newcollectionsFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $jsonEncoder;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $categoryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Simi\Simiconnector\Helper\Website $websiteHelper,
        \Simi\Simicustomize\Model\NewcollectionsFactory $newcollectionsFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        array $data = []
    ) {
   
        $this->simiObjectManager    = $simiObjectManager;
        $this->newcollectionsFactory = $newcollectionsFactory;
        $this->websiteHelper        = $websiteHelper;
        $this->systemStore          = $systemStore;
        $this->jsonEncoder          = $jsonEncoder;
        $this->categoryFactory      = $categoryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    public function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('newcollections');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Simi_Simiconnector::newcollections_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Newcollections Information')]);

        $data                = $model->getData();
        if ($model->getId()) {
            $fieldset->addField('newcollections_id', 'hidden', ['name' => 'newcollections_id']);
            $simicustomizehelper = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Data');
            $typeID              = $simicustomizehelper->getVisibilityTypeId('homecategory');
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

        $fieldset->addField(
            'newcollections_name',
            'text',
            [
                'name'     => 'newcollections_name',
                'label'    => __('Name'),
                'title'    => __('Name'),
                // 'class'    => 'validate-not-negative-number'
            ]
        );

        $storeResourceModel = $this->simiObjectManager
                ->get('Simi\Simiconnector\Model\ResourceModel\Storeviewmultiselect');
        $fieldset->addField('storeview_id', 'multiselect', [
            'name'     => 'storeview_id[]',
            'label'    => __('Store View'),
            'title'    => __('Store View'),
            'required' => true,
            'values'   => $storeResourceModel->toOptionArray(),
        ]);

        $fieldset->addField(
            'newcollections_filename_0',
            'image',
            [
                'name'     => 'newcollections_filename_0',
                'label'    => __('Image 1 (width:1160px, height:775px)'),
                'title'    => __('Image 1 (width:1160px, height:775px)'),
                'disabled' => $isElementDisabled
            ]
        );

        // $fieldset->addField(
        //     'newcollections_filename_0_tablet',
        //     'image',
        //     [
        //         'name'     => 'newcollections_filename_tablet',
        //         'label'    => __('Tablet Image (width:220px, height:220px)'),
        //         'title'    => __('Tablet Image (width:220px, height:220px)'),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        $fieldset->addField('category_id_0', 'select', [
            'name'     => 'category_id_0',
            'label'    => __('Category 1'),
            'title'    => __('Category 1'),
            'required' => true,
            'values'   => $this->simiObjectManager->get('Simi\Simiconnector\Helper\Catetree')->getChildCatArray(),
        ]);

        $fieldset->addField(
            'newcollections_filename_1',
            'image',
            [
                'name'     => 'newcollections_filename_1',
                'label'    => __('Image 2 (width:572px, height:382px)'),
                'title'    => __('Image 2 (width:572px, height:382px)'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField('category_id_1', 'select', [
            'name'     => 'category_id_1',
            'label'    => __('Category 2'),
            'title'    => __('Category 2'),
            'required' => true,
            'values'   => $this->simiObjectManager->get('Simi\Simiconnector\Helper\Catetree')->getChildCatArray(),
        ]);

        $fieldset->addField(
            'newcollections_filename_2',
            'image',
            [
                'name'     => 'newcollections_filename_2',
                'label'    => __('Image 3 (width:572px, height:382px)'),
                'title'    => __('Image 3 (width:572px, height:382px)'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField('category_id_2', 'select', [
            'name'     => 'category_id_2',
            'label'    => __('Category 3'),
            'title'    => __('Category 3'),
            'required' => true,
            'values'   => $this->simiObjectManager->get('Simi\Simiconnector\Helper\Catetree')->getChildCatArray(),
        ]);

        $fieldset->addField(
            'newcollections_filename_3',
            'image',
            [
                'name'     => 'newcollections_filename_3',
                'label'    => __('Image 4 (width:572px, height:780px)'),
                'title'    => __('Image 4 (width:572px, height:780px)'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField('category_id_3', 'select', [
            'name'     => 'category_id_3',
            'label'    => __('Category 4'),
            'title'    => __('Category 4'),
            'required' => true,
            'values'   => $this->simiObjectManager->get('Simi\Simiconnector\Helper\Catetree')->getChildCatArray(),
        ]);

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 1;
        }
        $fieldset->addField(
            'sort_order',
            'text',
            [
            'name'     => 'sort_order',
            'label'    => __('Sort Order'),
            'title'    => __('Sort Order'),
            'class'    => 'validate-not-negative-number',
            'disabled' => $isElementDisabled
                ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
            'name'     => 'status',
            'label'    => __('Status'),
            'title'    => __('Status'),
            'required' => false,
            'disabled' => $isElementDisabled,
            'options'  => $this->newcollectionsFactory->create()->toOptionStatusHash(),
                ]
        );

        $this->_eventManager->dispatch('adminhtml_newcollections_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Newcollections Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Newcollections Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return true;
    }
}
