<?php

namespace Vnecoms\VendorsShipping\Block\Checkout\Cart;

class ShippingProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Vnecoms\VendorsShipping\Helper\Data
     */
    protected $helper;
    
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @param \Vnecoms\VendorsShipping\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Vnecoms\VendorsShipping\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ){
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }
    
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if(!$this->helper->isEnabled()) return $jsLayout;
        
        $jsLayout['components']['block-summary']['children']['block-rates']['component'] = 'Vnecoms_VendorsShipping/js/view/cart/shipping-rates';
        return $jsLayout;
    }
    
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
    
}
