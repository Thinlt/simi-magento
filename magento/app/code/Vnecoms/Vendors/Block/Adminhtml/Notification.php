<?php
/**
 * Catalog price rules
 *
 * @author      Vnecoms Team <core@vnecoms.com>
 */
namespace Vnecoms\Vendors\Block\Adminhtml;

class Notification extends \Magento\Backend\Block\Template
{
    /**
     * Length of notification description showed by default
     */
    const NOTIFICATION_DESCRIPTION_LENGTH = 150;
    
    /**
     * Notifications
     * @var array
     */
    protected $_notifications;
    /**
     * Get notification count
     * @return number
     */
    public function getNotificationCount()
    {
        return sizeof($this->getNotifications());
    }
    
    /**
     * Retrieve notification description start length
     *
     * @return int
     */
    public function getNotificationDescriptionLength()
    {
        return self::NOTIFICATION_DESCRIPTION_LENGTH;
    }
    
    /**
     * Get notifications
     * @return array:
     */
    public function getNotifications()
    {
        if (!$this->_notifications) {
            $this->_notifications = [];
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $transport = $om->create('Magento\Framework\DataObject');
            $transport->setNotifications($this->_notifications);
            $this->_eventManager->dispatch('ves_vendors_admin_notifications', ['transport'=>$transport]);
            $this->_notifications = $transport->getNotifications();
        }
        return $this->_notifications;
    }
}
