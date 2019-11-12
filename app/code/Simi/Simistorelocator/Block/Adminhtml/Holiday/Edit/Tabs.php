<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Holiday\Edit;

use Simi\Simistorelocator\Model\Factory;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('general_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Holiday Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();

        $this->addTab('general_section', 'holiday_edit_tab_general');

        // add stores tab
        $this->addTab(
                'stores_section', [
            'label' => __('Stores of Holiday'),
            'title' => __('Stores of Holiday'),
            'class' => 'ajax',
            'url' => $this->getUrl(
                    'simistorelocatoradmin/ajaxtabgrid_store', [
                'entity_type' => Factory::MODEL_HOLIDAY,
                'enitity_id' => $this->getRequest()->getParam('holiday_id'),
                'serialized_name' => 'serialized_stores',
                    ]
            ),
                ]
        );

        return $this;
    }
}
