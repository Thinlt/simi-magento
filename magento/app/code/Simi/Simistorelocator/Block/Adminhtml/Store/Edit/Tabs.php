<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {

    /**
     * construct.
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('store_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Information'));
        parent::_prepareLayout();
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

        // add general tab
        $this->addTab('general_section', 'store_edit_tab_general');

        // add Google Map tab
        $this->addTab('gmap_section', 'store_edit_tab_gmap');

        // add image gallery section
        $this->addTab('imagegallery_section', 'store_edit_tab_imagegallery');

        // add schedule tab
        $this->addTab('schedule_section', 'store_edit_tab_schedule');

        // add schedule tab
        $this->addTab(
                'tag_section', [
            'label' => 'Store\'s Tags',
            'title' => 'Store\'s Tags',
            'class' => 'ajax',
            'url' => $this->getUrl(
                    'simistorelocatoradmin/ajaxtabgrid_tag', [
                'method_getter_id' => \Simi\Simistorelocator\Model\Store::METHOD_GET_TAG_ID,
                'serialized_name' => 'serialized_tags',
                '_current' => true,
                    ]
            ),
                ]
        );

        // add holiday tab
        $this->addTab(
                'holiday_section', [
            'label' => 'Store\'s Holidays',
            'title' => 'Store\'s Holidays',
            'class' => 'ajax',
            'url' => $this->getUrl(
                    'simistorelocatoradmin/ajaxtabgrid_holiday', [
                'method_getter_id' => \Simi\Simistorelocator\Model\Store::METHOD_GET_HOLIDAY_ID,
                'serialized_name' => 'serialized_holidays',
                '_current' => true,
                    ]
            ),
                ]
        );

        // add specialday tab
        $this->addTab(
                'specialday_section', [
            'label' => 'Store\'s Special days',
            'title' => 'Store\'s Special days',
            'class' => 'ajax',
            'url' => $this->getUrl(
                    'simistorelocatoradmin/ajaxtabgrid_specialday', [
                'method_getter_id' => \Simi\Simistorelocator\Model\Store::METHOD_GET_SPECIALDAY_ID,
                'serialized_name' => 'serialized_specialdays',
                '_current' => true,
                    ]
            ),
                ]
        );

        return $this;
    }
}
