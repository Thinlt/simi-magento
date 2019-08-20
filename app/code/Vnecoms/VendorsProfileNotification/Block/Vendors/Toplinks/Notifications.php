<?php

namespace Vnecoms\VendorsProfileNotification\Block\Vendors\Toplinks;

use Vnecoms\VendorsProfileNotification\Model\ResourceModel\Process\CollectionFactory;
use Vnecoms\VendorsProfileNotification\Model\Process;

/**
 * Vendor Notifications block
 */
class Notifications extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var \Vnecoms\VendorsProfileNotification\Model\ResourceModel\Process\Collection
     */
    protected $collection;
    
    /**
     * @var array
     */
    protected $notCompleteProcesses;
    
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $session;
    
    /**
     * @var int
     */
    protected $notificationCount;
    
    /**
     * @var int
     */
    protected $pendingNotificationCount;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Vendors\Model\UrlInterface $url
     * @param \Vnecoms\Vendors\Model\Session $session
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\UrlInterface $url,
        \Vnecoms\Vendors\Model\Session $session,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $url, $data);
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
    }
    
    /**
     * Get Vendor
     * 
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor(){
        return $this->session->getVendor();
    }
    
    /**
     * Get notification collection
     * 
     * @return \Vnecoms\VendorsProfileNotification\Model\ResourceModel\Process\Collection
     */
    public function getCollection(){
        if(!$this->collection){
            $this->collection = $this->collectionFactory->create();
            $this->collection->addFieldToFilter('status', Process::STATUS_ENABLED)
                ->setOrder('sort_order','ASC');
        }
        
        return $this->collection;
    }
    
    /**
     * Get not completed processes
     * 
     * @return array:
     */
    public function getNotCompletedProcesses(){
        if(!isset($this->notCompleteProcesses)){
            $this->notCompleteProcesses = [];
            foreach($this->getCollection() as $process){
                if(!$process->isCompleted($this->getVendor())){
                    $this->notCompleteProcesses[] = $process;
                }
            }
        }
        
        return $this->notCompleteProcesses;
    }
    
    /**
     * Get notification count
     *  
     * @return int
     */
    public function getNotificationCount(){
        if(!isset($this->notificationCount)){
            $this->notificationCount = sizeof($this->getCollection());
        }
        return $this->notificationCount;
    }
    
    /**
     * Get pending notification count
     * 
     * @return int
     */
    public function getPendingNotificationCount(){
        if(!isset($this->notCompleteProcesses)){
            $this->pendingNotificationCount = sizeof($this->getNotCompletedProcesses());
        }
        return $this->pendingNotificationCount;
    }
    
    /**
     * Get percent of completed profile
     * 
     * @return number
     */
    public function getPercentOfCompletedProfile(){
        $pendingCount = $this->getPendingNotificationCount();
        $notificationCount = $this->getNotificationCount();
        $completedCount = $notificationCount - $pendingCount;
        return round(($completedCount * 100.0) / $notificationCount);
    }
    
    /**
     * Hide the notification when all profiles are completed.
     * 
     * @see \Magento\Framework\View\Element\Template::_toHtml()
     */
    protected function _toHtml(){
        if(!$this->getPendingNotificationCount()) return '';
        return parent::_toHtml();
    }
}
