<?php

namespace Simi\Simistorelocator\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;

class AjaxTabGrid extends \Simi\Simistorelocator\Controller\Adminhtml\AbstractAction {

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute() {
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

        return $resultLayout;
    }

    /**
     * Check if admin has permissions to visit related pages.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi_Simistorelocator::storelocator');
    }

}
