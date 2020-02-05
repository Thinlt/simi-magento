<?php

namespace Vnecoms\VendorsApi\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Vnecoms\VendorsApi\Api\Data\Credit\TransactionSearchResultInterfaceFactory as SearchResultFactory;
use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory as VendorOrderCollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Vendor repository.
 */
class CreditRepository implements \Vnecoms\VendorsApi\Api\CreditRepositoryInterface
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
     * @var OrderRepositoryInterface
     */
    protected $orderResponsy;
    
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    
    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;
    
    /**
     * @param ApiHelper $helper
     * @param SearchResultFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ApiHelper $helper,
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->helper = $helper;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class);
    }
    

    /**
     * @see \Vnecoms\VendorsApi\Api\DashboardRepositoryInterface::getLastOrders()
     */
    public function getTransactions(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        /** @var \Vnecoms\VendorsApi\Api\Data\Credit\TransactionSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $om = ObjectManager::getInstance();
        
        $filter  = $om->get('Magento\Framework\Api\Filter');
        $filter->setField('customer_id');
        $filter->setValue($customerId);
        $filter->setConditionType('eq');
        $filterGroup = $om->get('Magento\Framework\Api\Search\FilterGroup');
        $filterGroup->setFilters([$filter]);
        
        $filterGroups = $searchCriteria->getFilterGroups();
        $filterGroups[] = $filterGroup;
        $searchCriteria->setFilterGroups($filterGroups);
        
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        
        return $searchResult;
    }
    
}
