<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\VendorMapping\Model\Api;

use Simi\VendorMapping\Api\VendorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Vendor implements VendorInterface
{
    const DEFAULT_DIR = 'desc';
    const DEFAULT_LIMIT = 15;
    const DIR = 'dir';
    const ORDER = 'order';
    const PAGE = 'page';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const FILTER = 'filter';
    const LIMIT_COUNT = 200;
    const VENDOR_IDS = 'ids'; //Filter by ids ex: 1,2,3

    /**
     * \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $_vendorFactory;

    /**
     * \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection
     */
    protected $_collection;

    /**
     * \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_configHelper;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageDatabase;
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

    protected $storeManager;
    protected $vendorHelper;
    protected $reviewHelper;

    protected $_logoConfig;
    protected $_bannerConfig;
    public $simiObjectManager;

    /**
     * Review product collection factory
     *
     * @var \Simi\VendorMapping\Model\ResourceModel\Review\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    protected $productRepositoryFactory;

    public function __construct(
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection $collection,
        \Magento\Framework\App\RequestInterface $request,
        \Vnecoms\VendorsConfig\Helper\Data $configHelper,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDatabase,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Simi\Simicustomize\Helper\Vendor $vendorHelper,
        \Simi\VendorMapping\Helper\Review $reviewHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Simi\VendorMapping\Model\ResourceModel\Review\Product\CollectionFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
        DirectoryList $directory_list
    ){
        $this->_vendorFactory = $vendorFactory;
        $this->_collection = $collection;
        $this->_request = $request;
        $this->_configHelper = $configHelper;
        $this->_fileStorageDatabase = $fileStorageDatabase;
        $this->_mediaDirectory = $context->getFilesystem()->getDirectoryRead(DirectoryList::MEDIA);
        $this->_storeManager = $storeManager;
        $this->vendorHelper = $vendorHelper;
        $this->reviewHelper = $reviewHelper;
        $this->simiObjectManager = $simiObjectManager;
        $this->productCollectionFactory = $productFactory;
        $this->productRepositoryFactory = $productRepositoryFactory;
    }

    /**
     * Vendor api VnecomsVendor module
     * @param int $id The Vendor ID.
     * @return array | json
     */
    public function getVendorDetail($id){
        $vendor = $this->_vendorFactory->create()->load($id);
        if ($vendor->getId()) {
            $data = $vendor->toArray();
            $data['logo'] = $this->getLogoUrl($vendor->getId());
            $data['logo_path'] = $this->getLogoPath($vendor->getId()) ? '/'. \Magento\Framework\UrlInterface::URL_TYPE_MEDIA . '/' .$this->getLogoPath($vendor->getId()) : '';
            $data['banner'] = $this->getBannerUrl($vendor->getId());
            $data['banner_path'] = $this->getBannerPath($vendor->getId()) ? '/'. \Magento\Framework\UrlInterface::URL_TYPE_MEDIA . '/' .$this->getBannerPath($vendor->getId()) : '';
            $data['profile'] = $this->vendorHelper->getProfile($vendor->getId());
            $data['reviews'] = $this->reviewHelper->getVendorReviews($id, false);
            $data['about'] = $this->getAbout($id);
            $data['faqs'] = $this->getFaqs($id);
            return array('data' => $data);
        }
        return false;
    }

    /**
     * Vendor api VnecomsVendor module
     * @param int $id The Vendor ID.
     * @return array | json
     */
    public function getVendorReviews($id){
        $data = array();
        $collection = $this->reviewHelper->getProductReviews($id, false);
        $page       = 1;
        $limit = self::DEFAULT_LIMIT;
        $offset = 0;
        $parameters = $this->_request->getParams();
        $this->setPageSize($parameters, $limit, $offset, $collection, $page);
        $info    = [];
        $total   = $collection->getSize();
        
        if ($offset > $total) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Invalid method.'), 4);
        }
        
        $fields = [];
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $count   = null;
        $check_limit  = 0;
        $check_offset = 0;

        // get all products to map to result
        $allProducts = [];
        $storeId = $this ->_storeManager->getStore()->getId();
        $products = $this->productCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addStatusFilter('1')
            ->addAttributeToFilter('rt.vendor_id', $id);
        foreach ($products as $product) {
            $allProducts[$product->getId()] = $product;
        }

        foreach ($collection as $entity) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit) {
                break;
            }
            $y = 0;
            foreach ($entity->getRatingVotes() as $vote) {
                $y += ($vote->getPercent() / 20);
            }
            $count = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')
                ->countArray($entity->getRatingVotes());
            $count = $count == 0 ? 1 : $count;
            $info_detail                = $entity->toArray($fields);
            $info_detail['rate_points'] = $x = (int) ($y / $count);
            if (isset($allProducts[$entity->getEntityPkValue()])) {
                $product = $allProducts[$entity->getEntityPkValue()];
                $productImage = $this->productRepositoryFactory->create()->getById($product->getId());
                $info_detail['product_id'] = $product->getId();
                $info_detail['product_name'] = $product->getName();
                $info_detail['product_image'] = $productImage->getData('thumbnail');
                $info_detail['product_url_key'] = preg_replace('/^http[s]?:\/\/.*?\//', '/', $product->getUrlModel()->getUrl($product));
            }
            $info_detail['product_url'] = $entity->getProductUrl($entity->getEntityPkValue(), $storeId);
            $info[] = $info_detail;
        }

        return array('data' => array(
            'reviews' => $info,
            'total' => $total, 'page_size' => $limit, 'from' => $offset
        ));
    }

    private function setPageSize($parameters, &$limit, &$offset, $collection, &$page)
    {
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }
        if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
            $limit = $parameters[self::LIMIT];
        }
        $offset = $limit * ($page - 1);
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        $collection->setPageSize($offset + $limit);
        if (isset($parameters['dir']) && isset($parameters['order'])) {
            $this->_order($parameters);
        }
    }

    /**
     * Vendor list api VnecomsVendor module
     * @return array | json
     */
    public function getVendorList(){
        $vendors = [];
        $this->_buildLimit();
        $vendorIds = $this->_request->getParam(self::VENDOR_IDS);
        $postData = $this->_request->getContent();
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData[self::VENDOR_IDS]) && $postData[self::VENDOR_IDS]) {
                $vendorIds = $postData[self::VENDOR_IDS];
            }
        }
        if ($this->_collection) {
            if ($vendorIds) {
                $vendor_ids = explode(',', $vendorIds);
                if (count($vendor_ids)) {
                    $this->_collection->addFieldToFilter('entity_id', array('FINSET', $vendor_ids));
                }
            }
            // $this->_collection->getSelect()->joinLeft(
            //     ['vendor_config' => $this->_collection->getTable('ves_vendor_config')],
            //     'vendor_config.vendor_id = e.entity_id AND vendor_config.store_id = 0 AND vendor_config.path = "general/store_information/logo"',
            //     ['vendor_config.value AS logo']
            // );
            foreach ($this->_collection as $vendor) {
                $vendorData = $vendor->toArray();
                $vendorData['logo'] = $this->getLogoUrl($vendor->getId());
                $vendorData['profile'] = $this->vendorHelper->getProfile($vendor->getId());
                $vendors[] = $vendorData;
            }
        }
        if (!count($vendors)) {
            return false;
        }
        return $vendors;
    }

    protected function _buildLimit(){
        if ($this->_collection) {
            $parameters = $this->_request->getParams();
            $postContent = $this->_request->getContent();
            if ($postContent) {
                $parameters = json_decode($postContent, true);
            }
            $page       = 1;
            if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
                $page = $parameters[self::PAGE];
            }
    
            $limit = self::DEFAULT_LIMIT;
            if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
                $limit = $parameters[self::LIMIT];
            }
    
            $offset = $limit * ($page - 1);
            if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
                $offset = $parameters[self::OFFSET];
            }
            $this->_collection->setPageSize($offset + $limit);
        }
    }

    /**
     * Get Seller Logo Image URL
     *
     * @param void
     * @return string
     */
    protected function getLogoUrl($vendorId)
    {
        $path = $this->getLogoPath($vendorId);
        if ($this->checkIsFile($path)) {
            return $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$path;
        }
        return '';
    }

    /**
     * Get Seller Logo Image URL
     *
     * @param void
     * @return string
     */
    protected function getLogoPath($vendorId)
    {
        if(!$this->_logoConfig){
            $this->_logoConfig = $this->_configHelper->getVendorConfig('general/store_information/logo', $vendorId);
        }
        if ($this->_logoConfig) {
            return 'ves_vendors/logo/' . $this->_logoConfig;
        }
        return '';
    }

    /**
     * Get Seller Logo Image URL
     *
     * @param void
     * @return string
     */
    protected function getBannerUrl($vendorId)
    {
        $path = $this->getBannerPath($vendorId);
        if ($this->checkIsFile($path)) {
            return $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$path;
        }
        return '';
    }

    protected function getBannerPath($vendorId)
    {
        if(!$this->_bannerConfig){
            $this->_bannerConfig = $this->_configHelper->getVendorConfig('general/store_information/banner', $vendorId);
        }
        if ($this->_bannerConfig) {
            return 'ves_vendors/banner/' . $this->_bannerConfig;
        }
        return '';
    }

    /**
     * Get about store infomation
     *
     * @param void
     * @return string
     */
    protected function getAbout($vendorId)
    {
        return $this->_configHelper->getVendorConfig('general/store/about', $vendorId);
    }

    /**
     * Get Faqs store
     *
     * @param void
     * @return string
     */
    protected function getFaqs($vendorId)
    {
        return $this->_configHelper->getVendorConfig('general/store/faqs', $vendorId);
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename relative file path
     * @return bool
     */
    protected function checkIsFile($filename)
    {
        if ($this->_fileStorageDatabase->checkDbUsage() && !$this->_mediaDirectory->isFile($filename)) {
            $this->_fileStorageDatabase->saveFileToFilesystem($filename);
        }
        return $this->_mediaDirectory->isFile($filename);
    }
}
