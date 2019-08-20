<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Block\Vendors\Withdraw;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Number;
/**
 * Vendor Notifications block
 */
class Review extends \Vnecoms\VendorsCredit\Block\Vendors\Withdraw\Form
{
    /**
     * Get Back Url
     *
     * @return string
     */
    public function getBackUrl(){
        return $this->getUrl('credit/withdraw/form',['method' => $this->getPaymentMethod()->getCode()]);
    }
    
    /**
     * Get Action URL
     *
     * @return string
     */
    public function getActionUrl(){
        return $this->getUrl(
            'credit/withdraw/save'
        );
    }
    
    /**
     * Get current withdrawal amount
     * 
     * @return number
     */
    public function getAmount(){
        return $this->_coreRegistry->registry('amount');
    }
    
    /**
     * Get Fee Amount
     * 
     * @return number
     */
    public function getFeeAmount(){
        return $this->getPaymentMethod()->calculateFee($this->getAmount());
    }
}
