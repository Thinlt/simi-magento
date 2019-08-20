<?php

namespace Vnecoms\PdfPro\Model\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class PdfProConfigVersion.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class PdfProConfigVersion implements ObserverInterface
{
    protected $helper;

    /**
     * PdfProConfigVersion constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     */
    public function __construct(\Vnecoms\PdfPro\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getTransport();
        $advancedPdfProcessor = array('label' => __('PDF Invoice Pro version'), 'value' => $this->helper->getVersion());
        $transport->setData('advancedpdfprocessor', $advancedPdfProcessor);
    }
}
