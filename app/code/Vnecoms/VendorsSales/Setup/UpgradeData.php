<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsSales\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     *
     * @var \Vnecoms\VendorsSales\Model\ResourceModel\OrderFactory
     */
    protected $_orderCollectionFactory;


    /**
     * Constructor
     *
     * @param \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0.1', '<')) {
            $collection = $this->_orderCollectionFactory->create();
            foreach ($collection as $order) {
                $items = $order->getAllVisibleItems();
                $order->setData('total_qty_ordered', sizeof($items))->save();
            }
        }
        
        $setup->endSetup();
    }
}
