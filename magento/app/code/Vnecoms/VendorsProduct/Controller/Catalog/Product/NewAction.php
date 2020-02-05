<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Controller\Catalog\Product;

use Vnecoms\Vendors\App\Action\Frontend\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

class NewAction extends \Vnecoms\VendorsProduct\Controller\Catalog\Product
{
    /**
     * @var Initialization\StockDataFilter
     */
    protected $stockFilter;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param Builder $productBuilder
     * @param StockDataFilter $stockFilter
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        StockDataFilter $stockFilter,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->stockFilter = $stockFilter;
        parent::__construct($context, $productBuilder);
        
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }
    

    /**
     * Create new product page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $product = $this->productBuilder->build($this->getRequest());

        $productData = $this->getRequest()->getPost('product');
        if (!$productData) {
            $sessionData = $this->_session->getProductData(true);
            if (!empty($sessionData['product'])) {
                $productData = $sessionData['product'];
            }
        }
        if ($productData) {
            $stockData = isset($productData['stock_data']) ? $productData['stock_data'] : [];
            $productData['stock_data'] = $this->stockFilter->filter($stockData);
            $product->addData($productData);
        }
        $this->_eventManager->dispatch('vendor_catalog_product_new_action', ['product' => $product]);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        /*set product tabs template*/
        if ($this->_config->getValue('vendors/catalog/product_edit_tabs_template') == \Vnecoms\VendorsProduct\Model\Config\Source\Tab\Template::TEMPLATE_HORIZONTAL_TABS) {
            $resultPage->addHandle('vendors_catalog_product_edit_tabs_horizontal');
        }
        if ($this->getRequest()->getParam('popup')) {
            $resultPage->addHandle(['popup', 'catalog_product_' . $product->getTypeId()]);
        } else {
            $resultPage->addHandle(['catalog_product_' . $product->getTypeId()]);
            $title = $resultPage->getConfig()->getTitle();
            $title->prepend(__("Catalog"));
            $title->prepend(__("Manage Products"));
            $breadCrumbBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
            $breadCrumbBlock->addLink(__("Catalog"), __("Catalog"))
                ->addLink(__("Manage Products"), __("Manage Products"), $this->getUrl('catalog/product'))
                ->addLink(__("New Product"), __("New Product"));
        }
        
        $block = $resultPage->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }
        return $resultPage;
    }
}
