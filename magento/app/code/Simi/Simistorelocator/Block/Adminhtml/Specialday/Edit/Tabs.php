<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Specialday\Edit;

use Simi\Simistorelocator\Model\Factory;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('specialday_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Special day Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();

        $this->addTab('general_section', 'specialday_edit_tab_general');

        // add stores tab
        $this->addTab(
                'stores_section', [
            'label' => __('Stores of Special day'),
            'title' => __('Stores of Special day'),
            'class' => 'ajax',
            'url' => $this->getUrl(
                    'simistorelocatoradmin/ajaxtabgrid_store', [
                'entity_type' => Factory::MODEL_SPECIALDAY,
                'enitity_id' => $this->getRequest()->getParam('specialday_id'),
                'serialized_name' => 'serialized_stores',
                    ]
            ),
                ]
        );

        return $this;
    }

}
