<?php
/**
 * Credit product type implementation
 */
namespace Vnecoms\Credit\Model\Product\Type\Credit;

use Vnecoms\Credit\Model\Source\Type as CreditType;

class Price extends \Magento\Catalog\Model\Product\Type\Price
{
    /**
     * Get product final price
     *
     * @param   float $qty
     * @param   \Magento\Catalog\Model\Product $product
     * @return  float
     */
    public function getFinalPrice($qty, $product)
    {
        return parent::getFinalPrice($qty, $product);    
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPrice($product)
    {
        $creditType = $product->getData('credit_type');
        $price = 0;
        switch($creditType){
            case CreditType::TYPE_FIXED:
                $price = $product->getData('credit_price');
                break;
            case CreditType::TYPE_OPTION:
                $priceOptions = $product->getData('credit_value_dropdown');
                if(!is_array($priceOptions)){
                    $priceOptions = json_decode($priceOptions,true);
                }
                $price = current($priceOptions);
                $price = $price['credit_price'];
                break;
            case CreditType::TYPE_RANGE:
                $priceOptions = $product->getData('credit_value_custom');
                $creditRate = $product->getData('credit_rate');
                if(!is_array($priceOptions)){
                    $priceOptions = json_decode($priceOptions,true);
                }
                $price = $priceOptions['from']/$creditRate;
                break;
        }
        $product->setData('price',$price);
        
        return parent::getPrice($product);
    }
    
    /**
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Product $product
     * @param float|null $qty
     *
     * @return float
     */
    public function getBasePrice($product, $qty = null)
    {
        $storeCredit = $product->getCustomOption('store_credit');
        if(!$storeCredit) return parent::getBasePrice($product,$qty);
        
        $creditInfo = $storeCredit->getValue();
        $creditInfo = unserialize($creditInfo);
        
        $price = isset($creditInfo['credit_price'])?$creditInfo['credit_price']:0;

        return $price;
    }
}
