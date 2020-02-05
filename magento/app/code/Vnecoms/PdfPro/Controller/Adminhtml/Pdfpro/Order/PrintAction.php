<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Order;

use Vnecoms\PdfPro\Model\Order;
use Vnecoms\PdfPro\Helper\Data as Helper;

/**
 * Class PrintAction.
 *
 * @author VnEcoms team <vnecoms.com>
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
        $enable = $this->helper->getConfig('pdfpro/general/enabled');

        if ($enable == 0) {
            return;
        }

        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Api\OrderRepositoryInterface')->get($orderId);
            if ($order) {
                $orderData = $this->_objectManager->create('\Vnecoms\PdfPro\Model\Order')->initOrderData($order);
                $resultRedirect = $this->resultRedirectFactory->create();
                try {
                    $result = $this->helper->initPdf(array($orderData), 'order');
                    if ($result['success']) {
                        return $this->_fileFactory->create(
                            $this->helper->getFileName('order', $order).'.pdf',
                            $result['content'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    } else {
                        throw new \Exception($result['msg']);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('sales/order/view/order_id/'.$orderId);

                    return $resultRedirect;
                }
            }
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
