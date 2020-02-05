<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Product;

class Update extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_UNAPPROVED = 0;
    const STATUS_PENDING    = 1;
    const STATUS_APPROVED   = 2;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_store;
    
    /**
     * Prefix of model events names
     *
     * @var string
     */
    
    protected $_eventPrefix = 'vendor_product_update';
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update');
    }
    
    /**
     * Get store
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStore()
    {
        if (!$this->_store) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $om->create('Magento\Store\Model\StoreManagerInterface');
            $this->_store = $storeManager->getStore($this->getStoreId());
        }
        
        return $this->_store;
    }
}
