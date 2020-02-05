<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Tag\Edit\Tab;

class GeneralTab extends \Magento\Backend\Block\Widget\Form\Generic
        implements \Magento\Backend\Block\Widget\Tab\TabInterface {

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
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Simi\Simistorelocator\Model\Tag $model */
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('tag_');

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField('tag_id', 'hidden', ['name' => 'tag_id']);
        }

        $fieldset->addField(
                'tag_name', 'text', [
            'name' => 'tag_name',
            'label' => __('Tag Name'),
            'title' => __('Tag Name'),
            'required' => true,
                ]
        );

        $fieldset->addField(
                'tag_description', 'editor', [
            'name' => 'tag_description',
            'label' => __('Description'),
            'title' => __('Description'),
            'wysiwyg' => true,
                ]
        );

        $fieldset->addField(
                'tag_icon', 'image', [
            'name' => 'tag_icon',
            'label' => __('Icon'),
            'title' => __('Icon'),
            'note' => __('Recommended size: 400x600 px. Supported format: jpg, jpeg, gif, png.'),
                ]
        );

        if (is_array($model->getData('tag_icon'))) {
            $model->setData('tag_icon', $model->getData('tag_icon/value'));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * get registry model.
     *
     * @return \Simi\Simistorelocator\Model\Store | null
     */
    public function getRegistryModel() {
        return $this->_coreRegistry->registry('simistorelocator_tag');
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
