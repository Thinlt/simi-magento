<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Vnecoms\Credit\Block\Product\View\Type;

use Vnecoms\Credit\Model\Source\Type as CreditType;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Credit extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $arrayUtils,$data);
    }
    
    /**
     * Get Credit Type of the current product
     * @return int
     */
    public function getCreditType(){
        return $this->getProduct()->getData('credit_type');
    }
    
    /**
     * Get all Credit Types
     * @return string
     */
    public function getCreditTypesJSON(){
        $types = [
            'TYPE_FIXED' => CreditType::TYPE_FIXED,
            'TYPE_OPTION' => CreditType::TYPE_OPTION,
            'TYPE_RANGE' => CreditType::TYPE_RANGE,
        ];
        return json_encode($types);
    }
    /**
     * Has options
     * @return boolean
     */
    public function hasOptions(){       
        return in_array($this->getCreditType(), [CreditType::TYPE_OPTION, CreditType::TYPE_RANGE]);
    }
    
    /**
     * Is Option Type
     * @return boolean
     */
    public function isOptionType(){
        return $this->getCreditType() == CreditType::TYPE_OPTION;
    }
    
    /**
     * Is Range Type
     * @return boolean
     */
    public function isRangeType(){
        return $this->getCreditType() == CreditType::TYPE_RANGE;
    }
    
    /**
     * Is Fixed Type
     * @return boolean
     */
    public function isFixedType(){
        return $this->getCreditType() == CreditType::TYPE_FIXED;
    }
    
    /**
     * Get Credit Value Custom
     * @return array
     */
    public function getCreditValueCustom(){
        $options = array();
        $creditValue        = $this->getProduct()->getData('credit_value_custom');
        $options['from']    = isset($creditValue['from'])?(int)$creditValue['from']:0;
        $options['to']      = isset($creditValue['to'])?(int)$creditValue['to']:0;
        return $options;
    }
    
    
    /**
     * Get option JSON
     * @return string
     */
    public function getOptionsJSON(){
        $options = [];
        if($this->isOptionType()){
            /*Option Credit Type*/
            $creditValue = $this->getProduct()->getData('credit_value_dropdown');
            foreach($creditValue as $option){
                $options[] = [
                    'label' => $this->formatBasePrice($option['credit_value']),
                    'value' => $option['credit_value'],
                    'price' => $this->convertPrice($option['credit_price']),
                ];
            }
        }elseif($this->isRangeType()){
            /*Custom Credit Type*/
            $options = $this->getCreditValueCustom();
            $options['rate']    = $this->getProduct()->getData('credit_rate');
            $options['currency_rate'] = $this->priceCurrency->convert(1);
            $options['init_value'] = $options['from'];
        }
        
        return json_encode($options);
    }
    
    /**
     * Format price
     * @param int $number
     * @param string $includeContainer
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function formatPrice($number,$includeContainer=false){
        return $this->priceCurrency->format($number,$includeContainer);
    }
    
    /**
     * Convert the base currency price to current currency
     * @param float $amount
     * @return float
     */
    public function convertPrice($amount=0){
        return $this->priceCurrency->convert($amount);
    }
    
    /**
     * Format price to base currency
     * @param number $amount
     * @return string
     */
    public function formatBasePrice($amount=0){
        return $this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($amount, 2, [], false);
    }
}
