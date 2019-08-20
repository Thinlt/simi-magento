<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro;

use Vnecoms\PdfPro\Helper\Data as Helper;

/**
 * Class PrintsAction.
 *
 * @author VnEcoms team <vnecoms.com>
 */
class Prints extends \Magento\Backend\App\Action
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Helper $helper
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_print');
    }

    /**
     * @return ResponseInterface|void
     */
    public function execute()
    {
    }
}
