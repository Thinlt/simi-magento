<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportExcel.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class ExportExcel extends \Vnecoms\PdfPro\Controller\Adminhtml\Template
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $fileName = 'templates.xls';
        $content = $this->_view->getLayout()->createBlock('Vnecoms\PdfPro\Block\Adminhtml\Template\Grid')->getExcel();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::theme');
    }
}
