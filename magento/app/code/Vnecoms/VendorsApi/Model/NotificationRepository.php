<?php

namespace Vnecoms\VendorsApi\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Vnecoms\VendorsApi\Api\Data\NotificationSearchResultInterfaceFactory as SearchResultFactory;
use Vnecoms\VendorsApi\Helper\Data as ApiHelper;

/**
 * notification repository.
 */
class NotificationRepository implements \Vnecoms\VendorsApi\Api\NotificationRepositoryInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;
    
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
     * NotificationRepository constructor.
     * @param ApiHelper $helper
     * @param SearchResultFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ApiHelper $helper,
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->helper = $helper;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor ?: ObjectManager::getInstance()
            ->get(CollectionProcessorInterface::class);
    }


    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\NotificationSearchResultInterface
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        /** @var \Vnecoms\VendorsApi\Api\Data\NotificationSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $om = ObjectManager::getInstance();
        
        $filter  = $om->get('Magento\Framework\Api\Filter');
        $filter->setField('vendor_id');
        $filter->setValue($vendorId);
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

    /**
     * @param int $customerId
     * @return int
     */
    public function getUnreadCount($customerId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        
        $om = ObjectManager::getInstance();
        /** @var \Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection */
        $collection = $om->create('Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection');
        $collection->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('is_read', 0);
        
        return $collection->count();
    }

    /**
     * @param int $customerId
     * @return bool
     */
    public function markAllAsRead($customerId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $om = ObjectManager::getInstance();
        $collection = $om->create('Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection')
            ->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('is_read',0);
        foreach ($collection as $notification){
            $notification->setData('is_read',1);
            $notification->save();
        }
        return true;
    }

    /**
     * @param int $notificationId
     * @param int $customerId
     * @return bool
     */
    public function deleteById($notificationId, $customerId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $om = ObjectManager::getInstance();
        $collection = $om->create('Vnecoms\VendorsNotification\Model\Notification')->load($notificationId);
        if ($collection->getData('vendor_id') != $vendorId){
            throw new LocalizedException(__('You are not permitted to delete this notification'));
        }
        $collection->delete($notificationId);
        return true;
    }

    /**
     * @param int[] $notificationIds
     * @param int $customerId
     * @return bool
     */
    public function massDelete($notificationIds, $customerId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $om = ObjectManager::getInstance();
        $collection = $om->create('Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection')
            ->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('notification_id', ['in' => $notificationIds]);

        if (!$collection->count()){
            throw new LocalizedException(__('There is no notification to delete.'));
        }

        foreach ($collection as $notification){
            $notification->delete();
        }
        return true;
    }
}
