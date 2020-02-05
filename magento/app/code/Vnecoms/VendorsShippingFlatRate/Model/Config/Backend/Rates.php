<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsShippingFlatRate\Model\Config\Backend;

class Rates extends \Vnecoms\VendorsConfig\Model\Config
{
    /**
     * @return $this
     */
    public function beforeSave()
    {
        if (is_array($this->getValue())) {
            $data = [];
            $addedGroupIds = [];
            foreach ($this->getValue() as $value) {
                if (isset($value['delete']) && $value['delete']) {
                    continue;
                }

                $data[$value['sort_order']] = [
                    'identifier' => $value['identifier'],
                    'title' => isset($value['title'])?$value['title']:'',
                    'type' => isset($value['type'])?$value['type']:'',
                    'price' => isset($value['price'])?$value['price']:0,
                    'free_shipping' => isset($value['free_shipping'])?$value['free_shipping']:'',
                    'sort_order' => isset($value['sort_order'])?$value['sort_order']:'',
                ];
            }

            ksort($data);
            $this->setValue(serialize($data));
        }

        return parent::beforeSave();
    }
}
