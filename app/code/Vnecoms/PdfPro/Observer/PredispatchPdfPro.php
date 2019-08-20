<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class PredispatchPdfPro.
 */
class PredispatchPdfPro extends AbstractObserver
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * PredispatchPdfPro constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $managerInterface,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        $this->_helper = $helper;
        $this->messageManager = $managerInterface;
        $this->resultRedirectFactory = $redirectFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $active = false;
        if (is_dir($this->_helper->getPdfLibDir()) and file_exists($this->_helper->getPdfLibDir().'/vendor/autoload.php')) {
            $active = true;
        }

        if (!$active) {
            throw new \Exception('Please Download PDF Library Before Use.');

            return;
            $this->messageManager->addError(__('Please Download PDF Library Before Use.'));

            return $resultRedirect->setUrl('index');
        }

        return;
    }
}
