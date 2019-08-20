<?php
// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsNotification\Block\Vendors\Toplinks;

/**
 * Vendor Notifications block
 */
class Notifications extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * @var \Vnecoms\VendorsMessage\Model\ResourceModel\Message\Collection
     */
    protected $_unreadMessageCollection;
    
    /**
     * @var \Vnecoms\VendorsNotification\Model\NotificationFactory
     */
    protected $_notificationFactory;
    
    /**
     * @var \Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection
     */
    protected $_notificationCollection;
    
    /**
     * @var \Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection
     */
    protected $_unReachedNotificationCollection;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\UrlInterface $url,
        \Vnecoms\VendorsNotification\Model\NotificationFactory $notificaitonFactory,
        \Vnecoms\Vendors\Model\Session $vendorSession,
        array $data = []
    ) {
        parent::__construct($context, $url, $data);
        $this->_vendorSession = $vendorSession;
        $this->_notificationFactory = $notificaitonFactory;
    }
    
    /**
     * Get Notification Collection
     * 
     * @return \Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection
     */
    public function getNotificationCollection(){
        if(!$this->_notificationCollection){
            $this->_notificationCollection = $this->_notificationFactory->create()->getCollection();
            $this->_notificationCollection->addFieldToFilter('vendor_id', $this->_vendorSession->getVendor()->getId())
                ->setOrder('notification_id','DESC')
                ->setPageSize(10);
        }
        
        return $this->_notificationCollection;
    }
    
    /**
     * Get Unreached Notification Collection
     *
     * @return \Vnecoms\VendorsNotification\Model\ResourceModel\Notification\Collection
     */
    public function getUnreachedNotificationCollection(){
        if(!$this->_unReachedNotificationCollection){
            $this->_unReachedNotificationCollection = $this->_notificationFactory->create()->getCollection();
            $this->_unReachedNotificationCollection->addFieldToFilter('vendor_id', $this->_vendorSession->getVendor()->getId())
            ->addFieldToFilter('is_read', 0)
            ->setOrder('notification_id','DESC');
        }
    
        return $this->_unReachedNotificationCollection;
    }
    
    /**
     * Get unreached notifications count
     * 
     * @return int
     */
    public function getNotifiationCount(){
        return $this->getUnreachedNotificationCollection()->count();
    }
    
    /**
     * Get View notification URL
     * 
     * @param \Vnecoms\VendorsNotification\Model\Notification $notification
     * @return string
     */
    public function getViewUrl(\Vnecoms\VendorsNotification\Model\Notification $notification){
        return $this->getUrl('notification/index/view',['id' => $notification->getId()]);
    }
    
    /**
     * Get Mark All as Read URL
     * @return string
     */
    public function getMarkAllAsReadUrl(){
        return $this->getUrl('notification/index/markAllRead');
    }
    
    /**
     * Get view all notifications URL
     * 
     * @return string
     */
    public function getViewAllUrl(){
        return $this->getUrl('notification/index/index');
    }
    
    /**
     * CHeck if the account has permission to view notifications
     * 
     * @see \Magento\Framework\View\Element\Template::_toHtml()
     */
    protected function _toHtml(){
        
        $permission = new \Vnecoms\Vendors\Model\AclResult();
        $this->_eventManager->dispatch(
            'ves_vendor_check_acl',
            [
                'resource' => 'Vnecoms_Vendors::notifications',
                'permission' => $permission
            ]
        );
        
        return $permission->isAllowed()?parent::_toHtml():'';
    }
}
