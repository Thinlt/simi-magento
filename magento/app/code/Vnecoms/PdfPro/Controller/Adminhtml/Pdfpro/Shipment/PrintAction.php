<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Shipment;

use Vnecoms\PdfPro\Helper\Data as Helper;
use Magento\Framework\App\ResponseInterface;

/**
 * Class PrintAction.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class PrintAction extends \Magento\Backend\App\Action
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
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->_objectManager->create('Magento\Sales\Api\ShipmentRepositoryInterface')->get($shipmentId);
            if ($shipment) {
                $shipmentData = $this->_objectManager->create('\Vnecoms\PdfPro\Model\Order\Shipment')->initShipmentData($shipment);
                $resultRedirect = $this->resultRedirectFactory->create();
                try {
                    $result = $this->helper->initPdf(array($shipmentData), 'shipment');
                    if ($result['success']) {
                        return $this->_fileFactory->create(
                            $this->helper->getFileName('shipment', $shipment).'.pdf',
                            $result['content'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    } else {
                        throw new \Exception($result['msg']);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('sales/shipment/view/shipment_id/'.$shipmentId);

                    return $resultRedirect;
                }
            }
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
