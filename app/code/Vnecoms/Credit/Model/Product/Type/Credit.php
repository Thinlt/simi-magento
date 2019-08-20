<?php
/**
 * Credit product type implementation
 */
namespace Vnecoms\Credit\Model\Product\Type;

use Vnecoms\Credit\Model\Source\Type as CreditType;

class Credit extends \Magento\Catalog\Model\Product\Type\Virtual
{
    /**
     * Product type code
     */
    const TYPE_CODE = 'store_credit';
        
    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare options belonging to specific product type.
     *
     * @param  \Magento\Framework\DataObject $buyRequest
     * @param  \Magento\Catalog\Model\Product $product
     * @param  string $processMode
     * @return array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        $creditType = $product->getData('credit_type');
        
        switch($creditType){
            case CreditType::TYPE_FIXED:
                $options = [
                    'type' => $creditType, 
                    'credit_price' => $product->getData('credit_price'),
                    'credit_value' => $product->getData('credit_value_fixed'),
                ];
                break;
            case CreditType::TYPE_OPTION:
                $options = $buyRequest->getData('store_credit');
                $creditValue = isset($options['credit_value'])?$options['credit_value']:0;
                if(!$creditValue) return __('You need to choose options for your item.')->render();
                
                $priceOptions = $product->getData('credit_value_dropdown');
                if(!is_array($priceOptions)){
                    $priceOptions = json_decode($priceOptions,true);
                }
                $creditPrice = 0;
                foreach($priceOptions as $option){
                    if($creditValue == $option['credit_value']){
                        $creditPrice = $option['credit_price'];
                    }
                }
                $options['type'] = $creditType;
                $options['credit_price'] = $creditPrice;
                break;
            case CreditType::TYPE_RANGE:
                $options = $buyRequest->getData('store_credit');
                $creditValue = isset($options['credit_value'])?$options['credit_value']:0;
                if(!$creditValue) return __('You need to choose options for your item.')->render();
                
                $creditRate = $product->getData('credit_rate');
                $creditPrice = $creditValue/$creditRate;
                $creditPrice = round($creditPrice,2);
                $options['type'] = $creditType;
                $options['credit_price'] = $creditPrice;
                break;
                
        }
        $product->addCustomOption('store_credit', serialize($options));
        
        return parent::_prepareProduct($buyRequest, $product, $processMode);        
    }
    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getOrderOptions($product)
    {
        $options = parent::getOrderOptions($product);
        if ($attributesOption = $product->getCustomOption('store_credit')) {
            $data = unserialize($attributesOption->getValue());
            $options['store_credit'] = $data;
            $options['attributes_info'] = [
                ['label' => __("Credit Value").'', 'value' => $data['credit_value']],
            ];
        }
    
        return $options;
    }
    
    /**
     * Return true if product has options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function hasOptions($product)
    {
        return in_array($product->getData('credit_type'), [CreditType::TYPE_OPTION, CreditType::TYPE_RANGE]) || $product->getHasOptions();
    }
//     /**
//      * Check if product can be bought
//      *
//      * @param  \Magento\Catalog\Model\Product $product
//      * @return $this
//      * @throws \Magento\Framework\Exception\LocalizedException
//      */
//     public function checkProductBuyState($product)
//     {
//         parent::checkProductBuyState($product);
//         $option = $product->getCustomOption('info_buyRequest');
//         if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
//             $buyRequest = new \Magento\Framework\DataObject(unserialize($option->getValue()));
//             $attributes = $buyRequest->getSuperAttribute();
//             if (is_array($attributes)) {
//                 foreach ($attributes as $key => $val) {
//                     if (empty($val)) {
//                         unset($attributes[$key]);
//                     }
//                 }
//             }
//             if (empty($attributes)) {
//                 throw new \Magento\Framework\Exception\LocalizedException($this->getSpecifyOptionMessage());
//             }
//         }
//         return $this;
//     }
}
