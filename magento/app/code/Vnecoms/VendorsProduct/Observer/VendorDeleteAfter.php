<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsProduct\Helper\Data as ProductHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class VendorDeleteAfter implements ObserverInterface
{
    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $_productHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var array
     */
    protected $_copyAttributes;

    protected $_indexerFactory;

    protected $_stockRegistry;

    public function __construct(
        ProductHelper $productHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->_productHelper = $productHelper;
        $this->_product = $product;
        $this->_stockRegistry = $stockRegistry;
        $this->_eventManager = $eventManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_indexerFactory = $indexerFactory;
    }
    /**
     * Save product data for all child products
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product*/
        $vendor = $observer->getVendor();
        $vendor_id = $vendor->getId();
		
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');

        $products = $productCollection->addAttributeToSelect('*')->addFieldToFilter('vendor_id', $vendor_id)
            ->load();

        foreach ($products as $product) {
            $sku = $product->getSku();
            $qty = 0;
            $stockItem = $this->_stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty($qty);
            $this->_stockRegistry->updateStockItemBySku($sku, $stockItem);
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableCatalogProductEntity = $resource->getTableName('catalog_product_entity');

        $sql = "Update " . $tableCatalogProductEntity . " Set vendor_id = 0 where vendor_id = ".$vendor_id;
        $connection->query($sql);

        return $this;
    }
}
