<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Tag\Edit;

use Simi\Simistorelocator\Model\Factory;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('tag_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Tag Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();

        $this->addTab('general_section', 'tag_edit_tab_general');

        // add stores tab
        $this->addTab(
                'stores_section', [
            'label' => __('Stores of Tag'),
            'title' => __('Stores of Tag'),
            'class' => 'ajax',
            'url' => $this->getUrl(
                    'simistorelocatoradmin/ajaxtabgrid_store', [
                'entity_type' => Factory::MODEL_TAG,
                'enitity_id' => $this->getRequest()->getParam('tag_id'),
                'serialized_name' => 'serialized_stores',
                    ]
            ),
                ]
        );

        return $this;
    }
}
