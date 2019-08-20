<?php
/**
 * @category    Magento
 * @package     Magento_Sales
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Adminhtml\Order;

class View extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Prepare button and grid
     */
    protected function _prepareLayout()
    {
        $vendorId = null;
        $order = $this->getParentBlock()->getOrder();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $vendorOrderCollection = $om->create('Vnecoms\VendorsSales\Model\ResourceModel\Order\Collection')
            ->addFieldToFilter('order_id', $order->getId());

        if ($vendorOrderCollection->count()) {
            $this->removeButton('order_creditmemo');
            $this->removeButton('order_ship');
        }
        return parent::_prepareLayout();
    }
}
