<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget\Grid\Column;

use Simi\Simistorelocator\Block\Adminhtml\Widget\Grid\Column\AbstractCheckboxes;

class StoreCheckboxes extends AbstractCheckboxes {

    /**
     * {@inheritdoc}
     */
    public function getSelectedValues() {
        return $this->storelocatorHelper->getTreeSelectedStores();
    }
}
