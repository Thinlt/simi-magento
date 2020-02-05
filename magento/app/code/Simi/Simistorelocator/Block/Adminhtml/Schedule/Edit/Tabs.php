<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Schedule\Edit;

use Simi\Simistorelocator\Model\Factory;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {

    /**
     * construct.
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('schedule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Schedule Information'));
    }

    /**
     * Preparing global layout.
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();

        $this->addTab('general_section', 'schedule_edit_tab_general');

        // add stores tab
        $this->addTab(
                'stores_section', [
            'label' => __('Stores of Schedule'),
            'title' => __('Stores of Schedule'),
            'class' => 'ajax',
            'url' => $this->getUrl(
                    'simistorelocatoradmin/ajaxtabgrid_store', [
                'entity_type' => Factory::MODEL_SCHEDULE,
                'enitity_id' => $this->getRequest()->getParam('schedule_id'),
                'serialized_name' => 'serialized_stores',
                    ]
            ),
                ]
        );

        return $this;
    }
}
