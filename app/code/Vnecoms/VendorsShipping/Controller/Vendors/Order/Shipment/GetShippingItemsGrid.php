<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Controller\Vendors\Order\Shipment;

use Vnecoms\Vendors\App\AbstractAction;
use Magento\Framework\App\ResponseInterface;

class GetShippingItemsGrid extends \Vnecoms\Vendors\App\AbstractAction
{

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @param Action\Context $context
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context  $context,
        \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader $shipmentLoader
    ) {
        $this->shipmentLoader = $shipmentLoader;
        parent::__construct($context);
    }

    /**
     * Return grid with shipping items for Ajax request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($this->getRequest()->getParam('order_id'));
        $this->shipmentLoader->setOrderId($vendorOrder->getOrderId());
        $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
        $this->shipmentLoader->setVendorOrder($vendorOrder);
        $this->shipmentLoader->load();

        return $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Vnecoms\VendorsShipping\Block\Vendors\Order\Packaging\Grid'
            )->setIndex(
                $this->getRequest()->getParam('index')
            )->toHtml()
        );
    }
}
