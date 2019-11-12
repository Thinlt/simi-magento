<?php

namespace Simi\Simistorelocator\Controller\Adminhtml;

abstract class Store extends \Simi\Simistorelocator\Controller\Adminhtml\AbstractAction {

    /**
     * param id for crud action : edit,delete,save.
     */
    const PARAM_CRUD_ID = 'simistorelocator_id';

    /**
     * registry name.
     */
    const REGISTRY_NAME = 'simistorelocator_store';

    /**
     * Init page.
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage) {
        $resultPage->setActiveMenu('Simi_Simistorelocator::storelocator')
                ->addBreadcrumb(__('Store Locator'), __('Store Locator'))
                ->addBreadcrumb(__('Manage Store'), __('Manage Store'));

        return $resultPage;
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi_Simistorelocator::store');
    }
}
