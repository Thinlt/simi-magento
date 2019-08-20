<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Controller\Adminhtml\Sales\Shipment;

use Magento\Backend\App\Action;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var \Vnecoms\VendorsSales\Controller\Adminhtml\Sales\Shipment\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Action\Context $context
     * @param \Vnecoms\VendorsSales\Controller\Adminhtml\Sales\Shipment\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        Action\Context $context,
        \Vnecoms\VendorsSales\Controller\Adminhtml\Sales\Shipment\ShipmentLoader $shipmentLoader,
        \Magento\Framework\Registry $registry
    ) {
        $this->shipmentLoader = $shipmentLoader;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }


    /**
     * Shipment create page
     *
     * @return void
     */
    public function execute()
    {
        $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($this->getRequest()->getParam('vorder_id'));

        $this->shipmentLoader->setOrderId($vendorOrder->getOrderId());
        $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
        $this->shipmentLoader->setVendorOrder($vendorOrder);

        $shipment = $this->shipmentLoader->load();
        if ($shipment) {
            $comment = $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
            if ($comment) {
                $shipment->setCommentText($comment);
            }

            $this->_coreRegistry->register('vendor_order', $vendorOrder);

            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_Sales::sales_order');
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipments'));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Shipment (%1)', $vendorOrder->getVendor()->getVendorId()));
            $this->_view->renderLayout();
        } else {
            $this->_redirect('*/order/view', ['order_id' => $vendorOrder->getOrderId()]);
        }
    }
}
