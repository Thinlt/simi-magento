<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Order\Shipment;

use Magento\Sales\Model\Order\Email\Sender\ShipmentCommentSender;
use Magento\Framework\View\Result\LayoutFactory;

class AddComment extends \Vnecoms\Vendors\App\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_shipments';
    
    /**
     * @var \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var ShipmentCommentSender
     */
    protected $shipmentCommentSender;

    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader $shipmentLoader
     * @param ShipmentCommentSender $shipmentCommentSender
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context  $context,
        \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader $shipmentLoader,
        ShipmentCommentSender $shipmentCommentSender,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->shipmentLoader = $shipmentLoader;
        $this->shipmentCommentSender = $shipmentCommentSender;
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($context);
    }

    /**
     * Add comment to shipment history
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->getRequest()->setParam('shipment_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please enter a comment.')
                );
            }
            $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
            $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
            $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
            $shipment = $this->shipmentLoader->load();
            $shipment->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );

            $this->shipmentCommentSender->send($shipment, !empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();
            $resultLayout = $this->resultLayoutFactory->create();
            $resultLayout->addDefaultHandle();
            $response = $resultLayout->getLayout()->getBlock('shipment_comments')->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Cannot add new comment.')];
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
