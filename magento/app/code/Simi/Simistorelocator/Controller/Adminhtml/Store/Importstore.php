<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store;

use Magento\Framework\Controller\ResultFactory;

class Importstore extends \Simi\Simistorelocator\Controller\Adminhtml\Store {

    /**
     * Create new Store.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('Import Stores'));

        return $resultPage;
    }
}
