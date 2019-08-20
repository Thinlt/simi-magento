<?php

namespace Vnecoms\VendorsApi\Model\Data\Credit;


use Magento\Framework\App\ObjectManager;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class TransactionSearchResult extends \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\Collection implements
    \Vnecoms\VendorsApi\Api\Data\Credit\TransactionSearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;
    
    
    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }
    
    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }
    
    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }
    
    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }
    
    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        if (!$items) {
            return $this;
        }
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }
    
    /**
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\TransactionInterface []
     */
    public function getItems(){
        $om = ObjectManager::getInstance();
        $dataObjectHelper = $om->create('Magento\Framework\Api\DataObjectHelper');
        
        $items = parent::getItems();
        $result = [];
        foreach ($items as $item) {
            if($item instanceof \Vnecoms\VendorsApi\Api\Data\Credit\TransactionInterface){
                $result[] = $item;
            }else{
                $transObj = $om->create('Vnecoms\VendorsApi\Api\Data\Credit\TransactionInterface');
                $dataObjectHelper->populateWithArray(
                    $transObj,
                    $item->getData(),
                    \Vnecoms\VendorsApi\Api\Data\Credit\TransactionInterface::class
                );
                
                $result[] = $transObj;
            }
        }
        
        return $result;
    }
}
