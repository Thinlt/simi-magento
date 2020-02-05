<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Schedule;

use Magento\Framework\Controller\ResultFactory;

class ExportExcel extends \Simi\Simistorelocator\Controller\Adminhtml\AbstractExportAction {

    /**
     * file name to export.
     *
     * @return string
     */
    protected function _getFileName() {
        return 'schedules.xml';
    }

    /**
     * get content.
     *
     * @return string
     */
    protected function _getContent() {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $resultPage->getLayout()
                ->getChildBlock('simistorelocatoradmin.schedule.grid', 'grid.export');

        return $exportBlock->getExcelFile();
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi_Simistorelocator::schedule');
    }

}
