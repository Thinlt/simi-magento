<?php

namespace VnEcoms\PdfPro\Controller\Adminhtml\Key;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportXml.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class ExportXml extends \Vnecoms\PdfPro\Controller\Adminhtml\Key
{
    public function execute()
    {
        $fileName = 'key.xml';
        $content = $this->_view->getLayout()->createBlock('Vnecoms\PdfPro\Block\Adminhtml\Key\Grid')->getXml();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
