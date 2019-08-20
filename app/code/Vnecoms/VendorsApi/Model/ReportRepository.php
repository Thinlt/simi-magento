<?php

namespace Vnecoms\VendorsApi\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Vendor repository.
 */
class ReportRepository implements \Vnecoms\VendorsApi\Api\ReportRepositoryInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;
    
    /**
     * @var VendorOrderCollectionFactory
     */
    protected $vendorOrderCollectionFactory;
    
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    
    
    /**
     * @param ApiHelper $helper
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ApiHelper $helper,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->helper = $helper;
        $this->collectionProcessor = $collectionProcessor ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class);
    }
    /**
     * @see \Vnecoms\VendorsApi\Api\ProductRepositoryInterface::getBestSelling()
     */
    public function getBestSelling($customerId, $limit = 5){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        
        $om = ObjectManager::getInstance();        
        $collection = $this->getBestsellingCollection($vendorId, $limit);
        $items = [];
        foreach($collection as $item){
            $bestsellingItem = $om->create('Vnecoms\VendorsApi\Api\Data\Report\BestsellingInterface');
            $bestsellingItem->setId($item->getId());
            $bestsellingItem->setName($item->getName());
            $bestsellingItem->setQty($item->getQtyOrdered());
            $bestsellingItem->setPrice($item->getPrice());
            $items[] = $bestsellingItem;
        }
        $searchResult = $om->create('Vnecoms\VendorsApi\Api\Data\Report\BestsellingSearchResultInterface');
        $searchResult->setItems($items);
        $searchResult->setTotalCount(sizeof($items));
        
        return $searchResult;
    }
    
    /**
     * @param int $vendorId
     * @param int $limit
     * @return \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    public function getBestsellingCollection($vendorId, $limit = 5){
        $om = ObjectManager::getInstance();
        /** @\Vnecoms\VendorsSales\Model\ResourceModel\Order\Collection */
        $collection = $om->create('Magento\Reports\Model\ResourceModel\Product\Collection');
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('vendor_id', $vendorId);
        $resource = $collection->getResource();
        $collection->joinTable(
            ['order_items' => $resource->getTable('sales_order_item')],
            'product_id = entity_id',
            ['qty_ordered' => 'SUM(order_items.qty_ordered)'],
            null,
            'left'
        );
        
        $connection = $collection->getConnection();
        $orderJoinCondition = [
            'order.entity_id = order_items.order_id',
            $connection->quoteInto("order.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];
        
        $collection->getSelect()
        ->joinInner(
            ['order' => $resource->getTable('sales_order')],
            implode(' AND ', $orderJoinCondition),
            []
        )->where(
            'parent_item_id IS NULL'
        )->group(
            'order_items.product_id'
        )->order(
            'qty_ordered DESC'
        )->limit($limit);
        
        return $collection;
    }
    
    
    /**
     * @see \Vnecoms\VendorsApi\Api\ReportRepositoryInterface::getMostViewed()
     */
    public function getMostViewed($customerId, $limit = 5){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        
        $om = ObjectManager::getInstance();
        $collection = $this->getMostViewedCollection($vendorId, $limit);
        $items = [];
        foreach($collection as $item){
            $bestsellingItem = $om->create('Vnecoms\VendorsApi\Api\Data\Report\MostViewedInterface');
            $bestsellingItem->setId($item->getId());
            $bestsellingItem->setName($item->getName());
            $bestsellingItem->setViewCount($item->getViews());
            $bestsellingItem->setPrice($item->getPrice());
            $items[] = $bestsellingItem;
        }
        $searchResult = $om->create('Vnecoms\VendorsApi\Api\Data\Report\MostViewedSearchResultInterface');
        $searchResult->setItems($items);
        $searchResult->setTotalCount(sizeof($items));
        
        return $searchResult;
    }
    
    /**
     * @param int $vendorId
     * @param int $limit
     * @return \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    public function getMostViewedCollection($vendorId, $limit = 5){
        $om = ObjectManager::getInstance();
        /** @\Magento\Reports\Model\ResourceModel\Product\Collection */
        $collection = $om->create('Magento\Reports\Model\ResourceModel\Product\Collection');
        $connection = $collection->getConnection();
        
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('vendor_id', $vendorId);
        $resource = $collection->getResource();
        $collection->joinTable(
            ['report_table_views' => $resource->getTable('report_event')],
            'object_id = entity_id',
            ['views' => 'COUNT(report_table_views.event_id)'],
            null,
            'right'
        );
        
        $collection->getSelect()->group(
            'e.entity_id'
        )->order(
            'views DESC'
        )->limit($limit);
        
        return $collection;
    }
}
