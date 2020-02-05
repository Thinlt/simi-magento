<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Vendors\Model\Vendor;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class PendingVendorObserver implements ObserverInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    
    /**
     * Vendor collection
     * @var \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection
     */
    protected $_vendorCollection;
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection $vendorCollection,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_vendorCollection = $vendorCollection;
        $this->_vendorCollection->addAttributeToFilter('status', Vendor::STATUS_PENDING);
    }
    
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }
    
    /**
     * Get number of pending vendor
     * @return number
     */
    public function getNumberOfPendingVendor()
    {
        return $this->_vendorCollection->count();
    }
    
    /**
     * Add the notification if there are any vendor awaiting for approval.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendorCount    = $this->getNumberOfPendingVendor();
        if ($vendorCount <= 0) {
            return;
        }
        
        $transport      = $observer->getTransport();
        $notifications  = $transport->getNotifications();
        $om             = \Magento\Framework\App\ObjectManager::getInstance();
        $notification   = $om->create('Magento\Framework\DataObject');
        
        if ($vendorCount ==1) {
            $notification->setData([
                'title'=> __("Vendor Approval"),
                'description' => __("There is a vendor awaiting for your approval.<br /><a href=\"%1\">Click here</a> to review the vendor account.", $this->getUrl('vendors/index/index'))
            ]);
        } else {
            $notification->setData([
                'title'=> __("Vendor Approval"),
                'description' => __('There are <strong>%1</strong> vendors awaiting for your approval.<br /><a href="%2">Click here</a> to review the vendor accounts.', $vendorCount, $this->getUrl('vendors/index/index'))
            ]);
        }
        $notifications[] = $notification;
        $transport->setNotifications($notifications);
    }
}
