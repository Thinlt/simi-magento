<?php
namespace Vnecoms\SmTheme\Plugin;


class FilterProducts
{
    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    
    /**
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->eventManager = $eventManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Modify the product collection of Sm FilterProducts extension
     * 
     * @param \Sm\FilterProducts\Block\FilterProducts $subject
     * @param \Closure $method
     * @return unknown
     */
    public function aroundGetLoadedProductCollection(\Sm\FilterProducts\Block\FilterProducts $subject, \Closure $method){
        $collection = $method();
        $collection->addAttributeToFilter('approval', \Vnecoms\VendorsProduct\Model\Source\Approval::STATUS_APPROVED);
        if ($this->moduleManager->isOutputEnabled('Vnecoms_Quotation')) {
            $collection->addAttributeToSelect('ves_enable_order')
                ->addAttributeToSelect('ves_enable_quote');
        }elseif($this->moduleManager->isOutputEnabled('Vnecoms_VendorsListingFee')){
            $collection->addAttributeToFilter('listing_fee', \Vnecoms\VendorsListingFee\Model\Source\ListingFee::LISTING_FEE_PAID);
        }
        $this->eventManager->dispatch('sm_theme_filterproducts_collection', ['collection' => $collection]);
        return $collection;
    }
}