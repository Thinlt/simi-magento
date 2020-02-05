<?php

namespace Vnecoms\SmTheme\Observer\Sm;

use Magento\Framework\Event\ObserverInterface;

class FilterProducts implements ObserverInterface
{    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$block = $observer->getBlock();
    	$transport = $observer->getTransport();
    	$html = $transport->getHtml();
    	if ($block->getIsAddedQuoteCode()) return;
        if (!$this->moduleManager->isEnabled('Vnecoms_Quotation')) return;
    	
    	if($block instanceof \Sm\FilterProducts\Block\FilterProducts){
    	    $transport = $observer->getTransport();
    	    $productCollection = $block->getLoadedProductCollection();
    	    
    	    $html .= $this->getQuoteProductListHtml(
				$productCollection,
				[
					'containerSelector' => '.products-grid',
					'addToCartBtnSelector' => '.btn-action.btn-cart',
				],
    	    	'Vnecoms_Quotation/js/catalog/product-list-featured'
			);
    	    $block->setIsAddedQuoteCode(true);
    	    
    	} elseif ($block instanceof \Sm\ListingTabs\Block\ListingTabs){
    		$productCollection = $block->_isAjax()?$block->_ajaxLoad():$block->getProducts();
    		if($productCollection){
    		    $html .= $this->getQuoteProductListHtml(
    	           $productCollection,
    	           [
    	           		'containerSelector' => '.sm-listing-tabs',
    	           ],
	               'Vnecoms_Quotation/js/catalog/product-list-tabs'
               	);
    		}
    		$block->setIsAddedQuoteCode(true);
    	}
    	
    	$transport->setHtml($html);
    	
    }
    
    /**
     * Get additional html code for product list
     * 
     * @param unknown $productCollection
     * @param string $component
     * @param array $params
     * @return string
     */
    public function getQuoteProductListHtml(
    		$productCollection,
    		$params = [],
    		$component = 'Vnecoms_Quotation/js/catalog/product-list'
	) {
        $productData = [];
        foreach($productCollection as $product){
            $productData['product_'.$product->getId()] = [
                'order_mode' => (bool)$product->getData('ves_enable_order'),
                'quote_mode' => (bool)$product->getData('ves_enable_quote'),
            ];
        }
        $quoteData = [
            'productData' => $productData,
        ];
        $quoteData = array_merge($quoteData, $params);
        
        $quoteData = [
            $component => $quoteData
        ];
        return "<div class=\"ves_quotation_data\" data-mage-init='".json_encode($quoteData)."'></div>";
    }
}

