<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Helper\Form;

use Magento\Framework\AuthorizationInterface;

/**
 * Product form category field helper
 */
class Category extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category
{
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorData;
    
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        AuthorizationInterface $authorization,
        \Vnecoms\Vendors\Helper\Data $vendorData,
        array $data = []
    ) {
        parent::__construct(
            $factoryElement, 
            $factoryCollection, 
            $escaper, 
            $collectionFactory, 
            $backendData, 
            $layout, 
            $jsonEncoder, 
            $authorization,
            $data
        );
        
        $this->_vendorData = $vendorData;
    }
    
    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return [
            'source' => $this->_vendorData->getUrl('catalog/category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true
        ];
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        if (!$this->isAllowed()) {
            return '';
        }
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $this->_jsonEncoder->encode($this->_getSelectorOptions());
        $newCategoryCaption = __('New Category');

        $return = <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        require(["jquery", "mage/mage"], function($){
            $('#{$htmlId}-suggest').mage('treeSuggest', {$selectorOptions});
        });
    </script>
HTML;
        return $return /* . $button->toHtml() */;
    }
    /**
     * Whether permission is granted
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return true;
    }
}
