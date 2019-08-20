<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsCredit\Model\ResourceModel\Withdrawal\CollectionFactory;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class PendingWithdrawalObserver implements ObserverInterface
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
    protected $_withdrawalCollection;
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_withdrawalCollection = $collectionFactory->create();
        $this->_withdrawalCollection->addFieldToFilter('status', \Vnecoms\VendorsCredit\Model\Withdrawal::STATUS_PENDING);
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
    public function getNumberOfPendingWithdrawal()
    {
        return $this->_withdrawalCollection->count();
    }
    
    /**
     * Add the notification if there are any vendor awaiting for approval.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $requestCount    = $this->getNumberOfPendingWithdrawal();
        if ($requestCount <= 0) {
            return;
        }
        
        $transport      = $observer->getTransport();
        $notifications  = $transport->getNotifications();
        $om             = \Magento\Framework\App\ObjectManager::getInstance();
        $notification   = $om->create('Magento\Framework\DataObject');
        
        if ($requestCount ==1) {
            $notification->setData([
                'title'=> __("Withdrawal"),
                'description' => __("There is a withdrawal request awaiting for your approval.<br /><a href=\"%1\">Click here</a> to review the request.", $this->getUrl('vendors/credit_withdrawal/pending'))
            ]);
        } else {
            $notification->setData([
                'title'=> __("Seller Review"),
                'description' => __('There are %1 withdrawal requests awaiting for your approval.<br /><a href="%2">Click here</a> to review the requests.', sprintf('<strong style="color: #ef672f">%s</strong>', $requestCount), $this->getUrl('vendors/credit_withdrawal/pending'))
            ]);
        }
        $notifications[] = $notification;
        $transport->setNotifications($notifications);
    }
}
