<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Key;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportCsv.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class ExportCsv extends \Vnecoms\PdfPro\Controller\Adminhtml\Key
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $fileName = 'key.csv';
        $content = $this->_view->getLayout()->createBlock('Vnecoms\PdfPro\Block\Adminhtml\Key\Grid')->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
