<?php


namespace Simi\Simistorelocator\Controller\Adminhtml\Holiday;

use Magento\Framework\Controller\ResultFactory;

class ExportExcel extends \Simi\Simistorelocator\Controller\Adminhtml\AbstractExportAction
{
    /**
     * file name to export.
     *
     * @return string
     */
    protected function _getFileName()
    {
        return 'holidays.xml';
    }

    /**
     * get content.
     *
     * @return string
     */
    protected function _getContent()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
        $exportBlock = $resultPage->getLayout()
            ->getChildBlock('simistorelocatoradmin.holiday.grid', 'grid.export');

        return $exportBlock->getExcelFile();
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Simi_Simistorelocator::holiday');
    }
}
