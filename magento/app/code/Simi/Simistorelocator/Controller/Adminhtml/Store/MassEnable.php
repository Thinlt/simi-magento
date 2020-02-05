<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store;

use Simi\Simistorelocator\Model\ResourceModel\Store\Grid\StatusesArray;


class MassEnable extends \Simi\Simistorelocator\Controller\Adminhtml\Store
{
    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->massActionFilter->getCollection($this->_createMainCollection());

        foreach ($collection as $item) {
            $item->setStatus(StatusesArray::STATUS_ENABLED);
            $item->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been enabled.',
                $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
