<?php
namespace Vnecoms\VendorsDashboard\Block\Vendors\Dashboard\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Vnecoms\Vendors\Model\Session as VendorSession;
use Vnecoms\VendorsProduct\Model\Source\Approval;

class NewProducts extends \Magento\Framework\View\Element\Template
{
    /**
     * Number of product will be showing
     * @var int
     */
    protected $_numOfProducts = 4;
    
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
    
    /**
     * @var VendorSession
     */
    protected $_vendorSession;
    
    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;
    
    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $_imageBuilder;
    
    /**
     * constructor
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param VendorSession $vendorSession
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        VendorSession $vendorSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_collectionFactory = $collectionFactory;
        $this->_vendorSession = $vendorSession;
        $this->_catalogConfig = $catalogConfig;
        $this->_imageBuilder = $imageBuilder;
    }
    
    /**
     * Get block title
     *
     * @return string
     */
    public function getBlockTitle()
    {
        return __("Recently Added Products");
    }
   
   /**
    * Get Vendor
    *
    * @return \Vnecoms\Vendors\Model\Vendor
    */
    public function getVendor()
    {
        return $this->_vendorSession->getVendor();
    }
   
   /**
    * Set the number of products will be showing.
    *
    * @param int $size
    */
    public function setProductsSize($size)
    {
        $this->_numOfProducts = $size;
    }
   
   /**
    * Filter product collection
    * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    * @return void
    */
    public function filter($collection)
    {
        $collection
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();
    }

    /**
     * Get Image Id
     *
     * @return string
     */
    public function getImageId()
    {
        return $this->getData('image_id')?$this->getData('image_id'):'dashboard_new_product';
    }
    
    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $attributes = [])
    {
        $block = $this->_imageBuilder->setProduct($product)
            ->setImageId($this->getImageId())
            ->setAttributes($attributes)
            ->create();
        $block->setTemplate('Vnecoms_VendorsDashboard::product/image.phtml');
        return $block;
    }
    
   /**
    * Get product collection
    *
    * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
    */
    public function getProductCollection()
    {
        if (!$this->getData('product_collection')) {
            $productCollection = $this->_collectionFactory->create();
            $productCollection->addAttributeToFilter('vendor_id', $this->getVendor()->getId());
            $productCollection->setPageSize($this->_numOfProducts);
            $this->filter($productCollection);
            $productCollection->addAttributeToSelect('approval');
            $productCollection->addAttributeToSelect('description');
            $productCollection->setOrder('entity_id', 'desc');
           
            $this->setData('product_collection', $productCollection);
        }
       
        return $this->getData('product_collection');
    }
   
   /**
    * Get html class of product by approval attribute
    * @param int $status
    */
    public function getStatusClass($status)
    {
        switch ($status) {
            case Approval::STATUS_APPROVED:
                return 'bg-green';
            case Approval::STATUS_NOT_SUBMITED:
                return 'bg-black';
            case Approval::STATUS_PENDING:
                return 'bg-yellow';
            case Approval::STATUS_UNAPPROVED:
                return 'bg-red';
        }
       
        return 'bg-yellow';
    }
   
   /**
    * Get product list URL
    *
    * @return string
    */
    public function getProductListUrl()
    {
        return $this->getUrl('catalog/product');
    }
   
   /**
    * Get Edit product url
    *
    * @param \Magento\Catalog\Model\Product $product
    * @return string
    */
    public function getEditUrl(\Magento\Catalog\Model\Product $product)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $product->getId()]);
    }
   /**
    * Format Price currency
    * @param float $amount
    * @return string
    */
    public function formatPrice($price)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($price, 2, [], false);
    }
   
   /**
    * Format description
    * @param string $description
    * @return string
    */
    public function formatDescription($description)
    {
        $description= strip_tags($description);
        $result = substr($description, 0, 40);
        if (strlen($result) < strlen($description)) {
            $result .= " ...";
        }
       
        return  $result;
    }
}
