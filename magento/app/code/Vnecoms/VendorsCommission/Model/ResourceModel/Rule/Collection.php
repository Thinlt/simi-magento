<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCommission\Model\ResourceModel\Rule;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'rule_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsCommission\Model\Rule', 'Vnecoms\VendorsCommission\Model\ResourceModel\Rule');
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $item->setData('vendor_group_ids', explode(",", $item->getData('vendor_group_ids')));
            $item->setData('website_ids', explode(",", $item->getData('website_ids')));
        }
        return $this;
    }
}
