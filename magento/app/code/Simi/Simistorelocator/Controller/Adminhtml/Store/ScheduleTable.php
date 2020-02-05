<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store;

use Magento\Framework\Controller\ResultFactory;

class ScheduleTable extends \Simi\Simistorelocator\Controller\Adminhtml\Store {

    public function execute() {
        /* @var \Magento\Framework\View\Result\Layout $response */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

        return $resultLayout;
    }

}
