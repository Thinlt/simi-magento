<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Vnecoms\VendorsProduct\Model\Source\Approval;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class PendingProductObserver implements ObserverInterface
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
    protected $_productCollection;
    
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
        $this->_productCollection = $collectionFactory->create();
        $this->_productCollection->addAttributeToFilter('approval', ['in' => [
            Approval::STATUS_PENDING,
            Approval::STATUS_PENDING_UPDATE,
        ]]);
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
    public function getNumberOfPendingProduct()
    {
        return $this->_productCollection->count();
    }
    
    /**
     * Add the notification if there are any vendor awaiting for approval.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productCount    = $this->getNumberOfPendingProduct();
        if ($productCount <= 0) {
            return;
        }
        
        $transport      = $observer->getTransport();
        $notifications  = $transport->getNotifications();
        $om             = \Magento\Framework\App\ObjectManager::getInstance();
        $notification   = $om->create('Magento\Framework\DataObject');
        
        if ($productCount ==1) {
            $notification->setData([
                'title'=> __("Product Approval"),
                'description' => __("There is a product awaiting for your approval.<br /><a href=\"%1\">Click here</a> to review the product.", $this->getUrl('vendors/catalog_product/index'))
            ]);
        } else {
            $notification->setData([
                'title'=> __("Product Approval"),
                'description' => __('There are <strong style="color: #ef672f">%1</strong> products awaiting for your approval.<br /><a href="%2">Click here</a> to review the products.', $productCount, $this->getUrl('vendors/catalog_product/index'))
            ]);
        }
        $notifications[] = $notification;
        $transport->setNotifications($notifications);
    }
}
