<?php

namespace VnEcoms\PdfPro\Controller\Adminhtml\Key;

/**
 * Class Grid.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Grid extends \Vnecoms\PdfPro\Controller\Adminhtml\Key
{
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
