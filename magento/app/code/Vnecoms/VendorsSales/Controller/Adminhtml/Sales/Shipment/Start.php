<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Controller\Adminhtml\Sales\Shipment;

class Start extends \Magento\Backend\App\Action
{

    /**
     * Start create shipment action
     *
     * @return void
     */
    public function execute()
    {
        /**
         * Clear old values for shipment qty's
         */
        $this->_redirect('vendors/sales_shipment/new', ['vorder_id' => $this->getRequest()->getParam('order_id')]);
    }
}
