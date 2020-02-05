<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Helper\Product;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Helper for fetching properties by product configurational item
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Configuration extends \Magento\Catalog\Helper\Product\Configuration
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Magento\Framework\Stdlib\StringUtils $string
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\Stdlib\StringUtils $string,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $productOptionFactory, $filter, $string);
    }
    /**
     * Retrieves product configuration options
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    public function getCustomOptions(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item)
    {
        $options = parent::getCustomOptions($item);
        $creditOption = $item->getOptionByCode('store_credit');
        if($creditOption){
            $creditOptionValue = $creditOption->getValue();
            $creditOptionValue = unserialize($creditOptionValue);
            $creditValue = isset($creditOptionValue['credit_value'])?$creditOptionValue['credit_value']:0;
        
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
