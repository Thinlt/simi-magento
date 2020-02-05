<?php

namespace VnEcoms\PdfPro\Block\Adminhtml\Key\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Store\Model\System\Store;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use Magento\Framework\Event\ManagerInterface;
use Magento\Customer\Model\Config\Source\Group\Multiselect;

class Form extends GenericForm implements TabInterface
{
    /**
     * Event manager.
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Customer\Model\Config\Source\Group\Multiselect
     */
    protected $customerGroup;
    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * @var BooleanOptions
     */
    protected $booleanOptions;

    /**
     * @param Store            $systemStore
     * @param Multiselect      $multiselect
     * @param ManagerInterface $eventManager
     * @param WysiwygConfig    $wysiwygConfig
     * @param BooleanOptions   $booleanOptions
     * @param Context          $context
     * @param Registry         $registry
     * @param FormFactory      $formFactory
     * @param array            $data
     */
    public function __construct(
        Store $systemStore,
        Multiselect $multiselect,
       // ManagerInterface $eventManager,
        WysiwygConfig $wysiwygConfig,
        BooleanOptions $booleanOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->systemStore = $systemStore;
        $this->customerGroup = $multiselect;
        //$this->eventManager = $eventManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \VnEcoms\PdfPro\Model\Key $model */
        $model = $this->_coreRegistry->registry('current_key');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('pdf_');
        $form->setFieldNameSuffix('pdf');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('API Key Information'),
                'class' => 'fieldset-wide',
            ]
        );

        $this->_eventManager->dispatch('ves_pdfpro_apikey_form_prepare_before', ['block' => $this]);

       // $fieldset->addType('image', 'Sample\News\Block\Adminhtml\Author\Helper\Image');
       // $fieldset->addType('file', 'Sample\News\Block\Adminhtml\Author\Helper\File');

        if ($model->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
        $fieldset->addField(
            'api_key',
            'text',
            [
                'name' => 'api_key',
                'label' => __('Api Key'),
                'title' => __('Api Key'),
                'required' => true,
            ]
        );
        /* Check is single store mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_ids',
                'multiselect',
                [
                    'name' => 'store_ids[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                ]
            );
        } else {
            $fieldset->addField(
                'stores_ids',
                'hidden',
                ['name' => 'store_ids[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreIds($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'name' => 'customer_group_ids[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'style' => 'min-width: 250px',
                'required' => true,
                'values' => $this->customerGroup->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'comment',
            'textarea',
            [
                'name' => 'comment',
                'label' => __('Comment'),
                'title' => __('Comment'),
                'style' => 'width:500px;height:200px;',
                'required' => false,
            ]
        );

        $fieldset->addField(
            'priority',
            'text',
            [
                'name' => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'required' => true,
            ]
        );

        //dispatch event
        $this->_eventManager->dispatch(
            'ves_pdfpro_apikey_form_prepare_after',
            ['router' => $this, 'fieldset' => $fieldset]
        );

        $data = $this->_session->getData('key_data', true);
        if ($data) {
            $model->addData($data);
        }
        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('API Key Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
