<?php

namespace Vnecoms\VendorsConfigApproval\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Vendors\Model\Vendor;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class PendingConfigApprovalObserver implements ObserverInterface
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
    protected $_configPendingCollection;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        \Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config\Pending\Collection $configPendingCollection,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_configPendingCollection = $configPendingCollection;
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
    public function getNumberOfPendingConfig()
    {
        return $this->_configPendingCollection->count();
    }

    /**
     * Add the notification if there are any vendor awaiting for approval.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $pendingConfigCount    = $this->getNumberOfPendingConfig();
        if ($pendingConfigCount <= 0) {
            return;
        }

        $transport      = $observer->getTransport();
        $notifications  = $transport->getNotifications();
        $om             = \Magento\Framework\App\ObjectManager::getInstance();
        $notification   = $om->create('Magento\Framework\DataObject');

        if ($pendingConfigCount ==1) {
            $notification->setData([
                'title'=> __("Config Approval"),
                'description' => __("There is a pending vendor config awaiting for your approval.<br /><a href=\"%1\">Click here</a> to review the pending configurations.", $this->getUrl('vendors/config_pending/index'))
            ]);
        } else {
            $notification->setData([
                'title'=> __("Config Approval"),
                'description' => __('There are <strong>%1</strong> pending vendor configs awaiting for your approval.<br /><a href="%2">Click here</a> to review the pending configurations.', $pendingConfigCount, $this->getUrl('vendors/config_pending/index'))
            ]);
        }
        $notifications[] = $notification;
        $transport->setNotifications($notifications);
    }
}
