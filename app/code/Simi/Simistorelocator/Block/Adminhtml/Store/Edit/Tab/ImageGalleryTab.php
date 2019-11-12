<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab;

class ImageGalleryTab extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

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
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('store_');

        /*
         * General Field Set
         */
        $fieldset = $form->addFieldset(
                'imagegallery_fieldset', [
            'legend' => __('Image Gallery'),
                ]
        );

        $fieldset->addField(
                'gallery', 'Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Element\Gallery', [
            'label' => __('Image Gallery'),
            'title' => __('Image Gallery'),
                ]
        )->setRenderer(
                $this->getLayout()->createBlock('Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Renderer\Gallery')
        );

        $form->setValues($model->getData());
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
        return __('Image Gallery');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle() {
        return __('Image Gallery');
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
