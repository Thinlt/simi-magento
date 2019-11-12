<?php

namespace Simi\Simistorelocator\Controller\Adminhtml;

abstract class Specialday extends \Simi\Simistorelocator\Controller\Adminhtml\AbstractAction {

    /**
     * param id for crud action : edit,delete,save.
     */
    const PARAM_CRUD_ID = 'specialday_id';

    /**
     * registry name.
     */
    const REGISTRY_NAME = 'simistorelocator_specialday';

    /**
     * Init page.
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage) {
        $resultPage->setActiveMenu('Simi_Simistorelocator::simistorelocator')
                ->addBreadcrumb(__('Store Locator'), __('Store Locator'))
                ->addBreadcrumb(__('Manage Special day'), __('Manage Special day'));

        return $resultPage;
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi_Simistorelocator::specialday');
    }

}
