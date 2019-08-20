<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Model\System\Message;

/**
 * Class MpdfOutOfDate.
 */
class MpdfOutOfDate implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * MpdfOutOfDate constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Retrieve unique message identity.
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('mpdf');
    }

    /**
     * Check whether.
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (is_dir($this->helper->getPdfLibDir()) and file_exists($this->helper->getPdfLibDir().'/vendor/autoload.php')) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve message text.
     *
     * @return string
     */
    public function getText()
    {
        $message = __('PDF Pro Library is missing now.').' ';
        $url = $this->helper->getConfig('pdfpro/general/download_lib');
        $message .= __('Please go to <a href="%1">Download EasyPDF Library</a> to download.', $url);

        return $message;
    }

    /**
     * Retrieve message severity.
     *
     * @return int
     */
    public function getSeverity()
    {
        return \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL;
    }
}
