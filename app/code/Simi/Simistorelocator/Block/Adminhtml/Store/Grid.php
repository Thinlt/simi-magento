<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store;

class Grid extends \Simi\Simistorelocator\Block\Adminhtml\Widget\Grid {

    /**
     * get selected row values.
     *
     * @return array
     */
    public function getSelectedRows() {
        $selectedStores = $this->converter->toFlatArray(
                $this->storelocatorHelper->getTreeSelectedStores()
        );
        return array_values($selectedStores);
    }
}
