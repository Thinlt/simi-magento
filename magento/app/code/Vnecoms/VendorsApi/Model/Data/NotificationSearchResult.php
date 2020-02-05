<?php

namespace Vnecoms\VendorsApi\Model\Data;


use Magento\Framework\App\ObjectManager;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class NotificationSearchResult extends \Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection implements
    \Vnecoms\VendorsApi\Api\Data\NotificationSearchResultInterface
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
     * @return \Vnecoms\VendorsApi\Api\Data\NotificationInterface []
     */
    public function getItems(){
        $om = ObjectManager::getInstance();
        $dataObjectHelper = $om->create('Magento\Framework\Api\DataObjectHelper');
        
        $items = parent::getItems();
        $result = [];
        foreach ($items as $item) {
            if($item instanceof \Vnecoms\VendorsApi\Api\Data\NotificationInterface){
                $result[] = $item;
            }else{
                $notificationObj = $om->create('Vnecoms\VendorsApi\Api\Data\NotificationInterface');
                $dataObjectHelper->populateWithArray(
                    $notificationObj,
                    $item->getData(),
                    \Vnecoms\VendorsApi\Api\Data\NotificationInterface::class
                );
                
                $result[] = $notificationObj;
            }
        }
        
        return $result;
    }
}
