<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\AbstractController;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class PrintShipment.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class PrintShipment extends \Magento\Framework\App\Action\Action
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
     * @var \Vnecoms\PdfPro\Model\Order\Shipment
     */
    protected $pdfProShipment;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * PrintShipment constructor.
     * @param Context $context
     * @param \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization
     * @param \Magento\Framework\Registry $registry
     * @param PageFactory $resultPageFactory
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Vnecoms\PdfPro\Model\Order $pdfOrder
     * @param \Vnecoms\PdfPro\Model\Order\Shipment $pdfShipment
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
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
        \Vnecoms\PdfPro\Model\Order\Shipment $pdfShipment,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
    ) {
        $this->orderAuthorization = $orderAuthorization;
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
        $this->pdfProOrder = $pdfOrder;
        $this->pdfProShipment = $pdfShipment;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;

        parent::__construct($context);
    }

    /**
     * Print Shipment Action.
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helper->getConfig('pdfpro/general/enabled') ||
            !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $shipmentId = (int) $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            $order = $shipment->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        }

        if ($this->orderAuthorization->canView($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($shipment)) {
                $this->_coreRegistry->register('current_shipment', $shipment);
            }
            $resultRedirect = $this->resultRedirectFactory->create();

            /*for alone invoice print*/
            if (isset($shipment)) {
                $shipmentData = $this->pdfProShipment->initShipmentData($shipment);

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
                    $resultRedirect->setPath('sales/order/shipment/order_id/'.$shipment->getOrderId());

                    return $resultRedirect;
                }
            } else {
                //for print all shipments
                if ($orderId) {
                    $shipmentCollectionFactory = $this->shipmentCollectionFactory->create()->addFieldToFilter('order_id', $orderId);
                    $shipmentData = [];
                    if ($shipmentCollectionFactory) {
                        foreach ($shipmentCollectionFactory as $shipment) {
                            $shipmentData[] = $this->pdfProShipment->initShipmentData($shipment);
                        }
                    }
                    try {
                        $result = $this->helper->initPdf($shipmentData, 'shipment');
                        if ($result['success']) {
                            return $this->_fileFactory->create(
                                $this->helper->getFileName('shipments').'.pdf',
                                $result['content'],
                                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                                'application/pdf'
                            );
                        } else {
                            throw new \Exception($result['msg']);
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $resultRedirect->setPath('sales/order/shipment/order_id/'.$orderId);

                        return $resultRedirect;
                    }
                }
            }
        } else {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
                $resultRedirect->setPath('sales/order/history');
            } else {
                $resultRedirect->setPath('sales/guest/form');
            }

            return $resultRedirect;
        }
    }
}
