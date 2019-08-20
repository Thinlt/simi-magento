<?php

namespace Vnecoms\VendorsPdf\Plugin\Shipment;

class PrintAction
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;
    
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;
    
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    
    /**
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ){
        $this->helper = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    
    /**
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Shipment\AbstractShipment\PrintAction $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function aroundExecute(
        \Vnecoms\VendorsSales\Controller\Vendors\Shipment\AbstractShipment\PrintAction $subject,
        \Closure $proceed
    ) {
        $shipmentId = $subject->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->objectManager->create('Magento\Sales\Api\ShipmentRepositoryInterface')->get($shipmentId);
            if ($shipment) {
                $shipmentData = $this->objectManager->create('\Vnecoms\PdfPro\Model\Order\Shipment')->initShipmentData($shipment);
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
