<?php

namespace Vnecoms\PdfPro\Model;

class PdfRenderer implements Api\PdfRendererInterface
{
    protected $_helper;

    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * get pdf content.
     *
     * @param string $type
     * @param array  $data
     *
     * @return string|null
     */
    public function getPdfContent($type, array $data)
    {
        $result = $this->_helper->initPdf($data, $type);
        if ($result['success']) {
            return $result['content'];
        }

        return;
    }

    /**
     * get file name pdf.
     *
     * @param string $type
     * @param $saleObject
     *
     * @return string
     */
    public function getFileName($type, $saleObject)
    {
        return $this->_helper->getFileName($type, $saleObject).'.pdf';
    }
}
