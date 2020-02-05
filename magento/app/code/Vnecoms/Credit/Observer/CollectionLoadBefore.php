<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Credit\Model\Processor\RefundByCredit;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class CollectionLoadBefore implements ObserverInterface
{
    /**
     * Before register credit memo, add credit to customer credit account.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();
        
//         if(get_class($collection) == 'Magento\Customer\Model\ResourceModel\Grid\Collection')
//         echo "<br /><hr /><br />".get_class($collection)."<br />";
    }
}
