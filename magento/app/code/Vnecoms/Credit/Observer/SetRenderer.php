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
class SetRenderer implements ObserverInterface
{
   
    
    /**
     * Add the notification if there are any vendor awaiting for approval. 
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $form = $observer->getForm();
        $layout = $observer->getLayout();
        
        $creditDropdown = $form->getElement('credit_value_dropdown');
        if ($creditDropdown) {
            $creditDropdown->setRenderer(
                $layout->createBlock('Vnecoms\Credit\Block\Adminhtml\Product\Edit\Renderer\Credit\Dropdown')
            );
        }
        
        $creditCustom = $form->getElement('credit_value_custom');
        if ($creditCustom) {
            $creditCustom->setRenderer(
                $layout->createBlock('Vnecoms\Credit\Block\Adminhtml\Product\Edit\Renderer\Credit\Custom')
            );
        }
        
        $creditPrice = $form->getElement('credit_price');
        if($creditPrice){
            $creditPrice->setCssClass('field-price');
        }
        
        $creditType = $form->getElement('credit_type');
        if($creditType){
            $creditType->setRenderer(
                $layout->createBlock('Vnecoms\Credit\Block\Adminhtml\Product\Edit\Renderer\Credit\Type')
            );
        }
        
        
    }
    
    
}
