<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Schedule;

use Magento\Framework\Controller\ResultFactory;

class Grid extends \Simi\Simistorelocator\Controller\Adminhtml\Schedule {

    /**
     * Order grid.
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute() {
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

        return $resultLayout;
    }

}
