<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\VendorMapping\Model\Api;

use Simi\VendorMapping\Api\VendorListInterface;

class VendorList implements VendorListInterface
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
     * \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection
     */
    protected $_collection;

    /**
     * \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    public function __construct(
        \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection $collection,
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->_collection = $collection;
        $this->_request = $request;
    }

    /**
     * Vendor list api VnecomsVendor module
     * @return array | json
     */
    public function getVendorList(){
        $vendors = [];
        $this->_buildLimit();
        $vendorIds = $this->_request->getParam(self::VENDOR_IDS);
        if ($this->_collection) {
            if ($vendorIds) {
                $vendor_ids = explode(',', $vendorIds);
                if (count($vendor_ids)) {
                    $this->_collection->addFieldToFilter('entity_id', array('FINSET', $vendor_ids));
                }
            }
            foreach ($this->_collection as $vendor) {
                $vendors[] = $vendor->toArray();
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
}
