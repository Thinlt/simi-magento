<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Vendors\Model\Vendor;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class RegisterCreditmemo implements ObserverInterface
{
   
    
    /**
     * Before register credit memo, add credit to customer credit account.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getCreditmemo();
        $input = $observer->getInput();
        if(isset($input['credit_refunded']) && $input['credit_refunded']){
            $order = $creditmemo->getOrder();
            $creditRefunded = $input['credit_refunded'];
            $creditmemo->setBaseCreditRefunded($creditRefunded);
            
            $creditRefunded = $order->getBaseCurrency()->convert($creditRefunded,$order->getOrderCurrency());
            $creditmemo->setCreditRefunded($creditRefunded);
        }
    }
    
    
}
