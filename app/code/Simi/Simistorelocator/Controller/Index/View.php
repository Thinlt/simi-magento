<?php

namespace Simi\Simistorelocator\Controller\Index;

class View extends \Simi\Simistorelocator\Controller\Index {

    /**
     * Execute action.
     */
    public function execute() {
        if (!$this->systemConfig->isEnableFrontend()) {
            return $this->_getResultRedirectNoroute();
        }

        $storelocatorId = $this->getRequest()->getParam('simistorelocator_id');

        /** @var \Simi\Simistorelocator\Model\Store $store */
        $store = $this->_objectManager->create('Simi\Simistorelocator\Model\Store')->load($storelocatorId);

        if (!$store->getId() || !$store->isEnabled()) {
            return $this->_getResultRedirectNoroute();
        }

        /*
         * load base image of store
         */

        $this->coreRegistry->register('simistorelocator_store', $store);

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_initResultPage();
        $resultPage->getConfig()->getTitle()->set($store->getMetaTitle());
        $resultPage->getConfig()->setDescription($store->getMetaDescription());
        $resultPage->getConfig()->setKeywords($store->getMetaKeywords());

        return $resultPage;
    }

}
