<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Specialday;

class MassEnable extends \Simi\Simistorelocator\Controller\Adminhtml\Specialday {

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute() {
        $collection = $this->massActionFilter->getCollection($this->_createMainCollection());

        foreach ($collection as $item) {
            $item->setStatus(\Simi\Simistorelocator\Model\Status::STATUS_ENABLED);
            $item->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been enabled.',
                $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }

}
