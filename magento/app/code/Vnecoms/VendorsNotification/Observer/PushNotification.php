<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsNotification\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsCredit\Model\CreditProcessor\OrderPayment;
use Vnecoms\VendorsCredit\Model\CreditProcessor\ItemCommission;

class PushNotification implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Vnecoms\VendorsNotification\Model\NotificationFactory
     */
    protected $_notificationFactory;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\VendorsNotification\Model\NotificationFactory $notificationFactory
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\VendorsNotification\Model\NotificationFactory $notificationFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_notificationFactory = $notificationFactory;
    }

    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var int */
        $vendorId = $observer->getVendorId();
        
        /** @var string */
        $notificationType = $observer->getType();
        
        /** @var string */
        $message = $observer->getMessage();
        
        /** @var array */
        $additionalInfo = $observer->getAdditionalInfo();
        if (!$additionalInfo) {
            $additionalInfo = [];
        }
        
        /* Save the notification */
        $notification = $this->_notificationFactory->create();
        $notification->setData([
            'vendor_id'     => $vendorId,
            'type'          => $notificationType,
            'message'       => $message,
            'additional_info'   => serialize($additionalInfo),
            'is_read'       => 0,
            'is_reached'    => 0,
        ])->save();
        
        return $this;
    }
}
