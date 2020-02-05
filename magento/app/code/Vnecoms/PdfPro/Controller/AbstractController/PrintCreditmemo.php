<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\AbstractController;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class PrintCreditmemo.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class PrintCreditmemo extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface
     */
    protected $orderAuthorization;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

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
     * @var \Vnecoms\PdfPro\Model\Order
     */
    protected $pdfProOrder;

    /**
     * @var \Vnecoms\PdfPro\Model\Order\Creditmemo
     */
    protected $pdfProCreditmemo;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory
     */
    protected $creditmemoCollectionFactory;


    /**
     * PrintCreditmemo constructor.
     * @param Context $context
     * @param \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization
     * @param \Magento\Framework\Registry $registry
     * @param PageFactory $resultPageFactory
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Vnecoms\PdfPro\Model\Order $pdfOrder
     * @param \Vnecoms\PdfPro\Model\Order\Creditmemo $pdfCreditmemo
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization,
        \Magento\Framework\Registry $registry,
        PageFactory $resultPageFactory,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Vnecoms\PdfPro\Model\Order $pdfOrder,
        \Vnecoms\PdfPro\Model\Order\Creditmemo $pdfCreditmemo,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollectionFactory
    ) {
        $this->orderAuthorization = $orderAuthorization;
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
        $this->pdfProOrder = $pdfOrder;
        $this->pdfProCreditmemo = $pdfCreditmemo;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Print Creditmemo Action.
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helper->getConfig('pdfpro/general/enabled') ||
            !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $creditmemoId = (int) $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->_objectManager->create('Magento\Sales\Api\CreditmemoRepositoryInterface')->get($creditmemoId);
            $order = $creditmemo->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        }

        if ($this->orderAuthorization->canView($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($creditmemo)) {
                $this->_coreRegistry->register('current_creditmemo', $creditmemo);
            }
            $resultRedirect = $this->resultRedirectFactory->create();

            /*for alone invoice print*/
            if (isset($creditmemo)) {
                $creditmemoData = $this->pdfProCreditmemo->initCreditmemoData($creditmemo);

                try {
                    $result = $this->helper->initPdf(array($creditmemoData), 'creditmemo');
                    if ($result['success']) {
                        return $this->_fileFactory->create(
                            $this->helper->getFileName('creditmemo', $creditmemo).'.pdf',
                            $result['content'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    } else {
                        throw new \Exception($result['msg']);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('sales/order/creditmemo/order_id/'.$creditmemo->getOrderId());

                    return $resultRedirect;
                }
            } else {
                //for print all refunds
                if ($orderId) {
                    $creditmemoDataCollection = $this->creditmemoCollectionFactory->create()->addFieldToFilter('order_id', $orderId);
                    $creditmemoData = [];
                    if ($creditmemoDataCollection) {
                        foreach ($creditmemoDataCollection as $creditmemo) {
                            $creditmemoData[] = $this->pdfProCreditmemo->initCreditmemoData($creditmemo);
                        }
                    }
                    try {
                        $result = $this->helper->initPdf($creditmemoData, 'creditmemo');
                        if ($result['success']) {
                            return $this->_fileFactory->create(
                                $this->helper->getFileName('creditmemos').'.pdf',
                                $result['content'],
                                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                                'application/pdf'
                            );
                        } else {
                            throw new \Exception($result['msg']);
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $resultRedirect->setPath('sales/order/creditmemo/order_id/'.$orderId);

                        return $resultRedirect;
                    }
                }
            }
        } else {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
                $resultRedirect->setPath('*/*/history');
            } else {
                $resultRedirect->setPath('sales/guest/form');
            }

            return $resultRedirect;
        }
    }
}
