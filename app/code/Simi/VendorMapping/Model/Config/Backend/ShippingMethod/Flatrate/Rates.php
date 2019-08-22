<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Model\Config\Backend\ShippingMethod\Flatrate;

/**
 * Flat product on/off backend
 */
class Rates extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $value = $this->getValue();
        $this->setValue($value);
    }

    /**
     * Set after commit callback
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        $data = $this->getValue();
        $this->setValue($data);
    }

    /**
     * Process flat enabled mode change
     *
     * @return void
     */
    // public function processValue()
    // {
    // }
}
