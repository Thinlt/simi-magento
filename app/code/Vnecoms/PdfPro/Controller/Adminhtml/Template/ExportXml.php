<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportXml.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class ExportXml extends \Vnecoms\PdfPro\Controller\Adminhtml\Template
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $fileName = 'templates.xml';
        $content = $this->_view->getLayout()->createBlock('Vnecoms\PdfPro\Block\Adminhtml\Template\Grid')->getXml();

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
