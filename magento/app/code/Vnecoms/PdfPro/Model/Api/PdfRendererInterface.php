<?php

namespace Vnecoms\PdfPro\Model\Api;

/**
 * Interface PdfRendererInterface.
 */
interface PdfRendererInterface
{
    /**
     * get pdf content.
     *
     * @param string $type
     * @param array  $data
     *
     * @return null|string
     */
    public function getPdfContent($type, array $data);

    /**
     * get pdf file name.
     *
     * @param string $type
     * @param $saleObject
     *
     * @return null|string
     */
    public function getFileName($type, $saleObject);
}
