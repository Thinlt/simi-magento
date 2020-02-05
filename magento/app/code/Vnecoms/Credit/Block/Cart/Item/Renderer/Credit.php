<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Cart\Item\Renderer;

use Magento\Checkout\Block\Cart\Item\Renderer;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Shopping cart item render block for configurable products.
 */
class Credit extends Renderer implements IdentityInterface
{
    /**
     * Get list of all options for product
     *
     * @return array
     */
    public function getOptionList()
    {
        $options = parent::getOptionList();
        $item = $this->getItem();
        $creditOption = $item->getOptionByCode('store_credit');
        if($creditOption){
            $creditOptionValue = $creditOption->getValue();
            $creditOptionValue = unserialize($creditOptionValue);
            $creditValue = isset($creditOptionValue['credit_value'])?$creditOptionValue['credit_value']:$creditOptionValue['credit_value'];
            
            if($creditValue){
                $creditValue = $this->priceCurrency->format($creditValue,false);
                $options[] = [
                    'label' => __("Credit Value"),
                    'value' => $creditValue,
                    'print_value' => $creditValue,
                    'option_id' => $creditOption->getId(),
                    'option_type' => 'text',
                    'custom_view' => '',
                ];
            }
        }
        
        
        return $options;
    }
}
