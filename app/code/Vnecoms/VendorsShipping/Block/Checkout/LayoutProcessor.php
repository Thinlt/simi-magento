<?php

namespace Vnecoms\VendorsShipping\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
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
        
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['component'] = 'Vnecoms_VendorsShipping/js/view/shipping';
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['template'] = 'Vnecoms_VendorsShipping/shipping';
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['removeAllVendorItemsUrl'] = $this->getUrl('vendorshipping/index/removeItems');
        
        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['shipping']['component'] = 'Vnecoms_VendorsShipping/js/view/summary/shipping';
        $jsLayout['components']['checkout']['children']['sidebar']['children']['shipping-information']['component'] = 'Vnecoms_VendorsShipping/js/view/shipping-information';
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
