<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsConfig\Model\ResourceModel\Config;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'config_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsConfig\Model\Config', 'Vnecoms\VendorsConfig\Model\ResourceModel\Config');
    }

    /**
     *  Add path filter
     *
     * @param string $section
     * @return $this
     */
    public function addPathFilter($section, $vendorId)
    {
        $this->addFieldToFilter('vendor_id', $vendorId);
        $this->addFieldToFilter('path', ['like' => $section . '/%']);
        return $this;
    }
}
