<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductFactory;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class UpdateVendorObserver implements ObserverInterface
{
    /**
     * Product Factory
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
    }
    
    /**
     * Set the vendor id in bulk for product
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $attrData = $observer->getAttributesData();
        if (isset($attrData['vendor_id'])) {
            $productIds = $observer->getProductIds();
            $resource = $this->_productFactory->create()->getResource();
            
            $adapter   = $resource->getConnection();
            $sql = "UPDATE ".$resource->getTable('catalog_product_entity').' SET vendor_id="'.$attrData['vendor_id'].'" WHERE entity_id IN('.implode(",", $productIds).')';
            $adapter->query($sql);
            
            unset($attrData['vendor_id']);
            $observer->getEvent()->setData('attributes_data', $attrData);
        }
    }
}
