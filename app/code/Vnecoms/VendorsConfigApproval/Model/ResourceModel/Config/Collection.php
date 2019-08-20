<?php

namespace Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'update_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsConfigApproval\Model\Config', 'Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config');
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
