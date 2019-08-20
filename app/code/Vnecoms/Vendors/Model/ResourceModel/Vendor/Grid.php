<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\ResourceModel\Vendor;

/**
 * App page collection
 */
class Grid extends \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection
{
    /**
     * Init select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
//         $this->getSelect()->joinLeft(
//             array('customer_address'=>$this->getTable('customer_address_entity')),
//             'e.ves_vendor_address=customer_address.entity_id',
//             array('telephone','postcode','country_id','region')
//         );
//         $this->addAttributeToSelect('telephone')
//             ->addAttributeToSelect('company')
//             ->addAttributeToSelect('city')
//             ->addAttributeToSelect('country_id')
//             ->addAttributeToSelect('postcode')
//             ->addAttributeToSelect('region')
//         ;
        return $this;
    }
}
