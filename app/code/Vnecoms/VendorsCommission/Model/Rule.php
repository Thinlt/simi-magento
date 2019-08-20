<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCommission\Model;

class Rule extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;
    
    const COMMISSION_BY_FIXED_AMOUNT            = 'by_fixed';
    const COMMISSION_BY_PERCENT_PRODUCT_PRICE   = 'by_percent';
    
    const COMMISSION_BASED_PRICE_INCL_TAX       = 'by_price_incl_tax';
    const COMMISSION_BASED_PRICE_EXCL_TAX       = 'by_price_excl_tax';
    const COMMISSION_BASED_PRICE_AFTER_DISCOUNT_INCL_TAX       = 'by_price_after_discount_incl_tax';
    const COMMISSION_BASED_PRICE_AFTER_DISCOUNT_EXCL_TAX       = 'by_price_after_discount_excl_tax';
    
    
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'vendors_commission';
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsCommission\Model\ResourceModel\Rule');
    }
    
}
