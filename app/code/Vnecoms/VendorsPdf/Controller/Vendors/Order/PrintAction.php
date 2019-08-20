<?php
namespace Vnecoms\VendorsPdf\Controller\Vendors\Order;

use Vnecoms\PdfPro\Model\Order;
use Vnecoms\PdfPro\Helper\Data as Helper;

/**
 * Class PrintAction.
 *
 * @author VnEcoms team <vnecoms.com>
 */
class PrintAction extends \Vnecoms\Vendors\App\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_orders';
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
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param Helper $helper
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
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
     * @return ResponseInterface|void
     */
    public function execute()
    {
        if (!$this->helper->isEnableModule()) {
            return $this->_redirect('sales/order');
        }

        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($orderId);

            if ($vendorOrder->getId()) {
                $orderData = $this->_objectManager->create('Vnecoms\VendorsPdf\Model\Order')->initVendorOrderData($vendorOrder);
                $resultRedirect = $this->resultRedirectFactory->create();
                try {
                    $result = $this->helper->initPdf(array($orderData), 'order');
                    if ($result['success']) {
                        return $this->_fileFactory->create(
                            $this->helper->getFileName('order', $vendorOrder).'.pdf',
                            $result['content'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    } else {
                        throw new \Exception($result['msg']);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('sales/order/view/',['order_id' => $orderId]);

                    return $resultRedirect;
                }
            }
        }
        return $this->resultForwardFactory->create()->forward('noroute');
    }
}
