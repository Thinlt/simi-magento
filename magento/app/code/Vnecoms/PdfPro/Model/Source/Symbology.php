<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

/**
 * Class Symbology.
 */
class Symbology implements ArrayInterface
{
    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            BarcodeGenerator::Codabar => __('Codabar'),
            BarcodeGenerator::Code11 => __('Code 11'),
            BarcodeGenerator::Code39 => __('Code 39'),
            BarcodeGenerator::Code39Extended => __('Code 39 Extended'),
            BarcodeGenerator::Code128 => __('Code 128'),
            BarcodeGenerator::Ean8 => __('EAN-8'),
            BarcodeGenerator::Ean13 => __('EAN-13'),
            BarcodeGenerator::Gs1128 => __('GS1-128 (EAN-128)'),
            BarcodeGenerator::Isbn => __('ISBN'),
            BarcodeGenerator::I25 => __('Interleaved 2 of 5'),
            BarcodeGenerator::S25 => __('Standard 2 of 5'),
            BarcodeGenerator::Msi => __('MSI Plessey'),
            BarcodeGenerator::Upca => __('UPC-A'),
            BarcodeGenerator::Upce => __('UPC-E'),
            BarcodeGenerator::Upcext2 => __('UPC Extenstion 2 Digits'),
            BarcodeGenerator::Upcext5 => __('UPC Extenstion 5 Digits'),
            BarcodeGenerator::Postnet => __('Postnet'),
            BarcodeGenerator::Intelligentmail => __('Intelligent Mail'),
        );
    }
}
