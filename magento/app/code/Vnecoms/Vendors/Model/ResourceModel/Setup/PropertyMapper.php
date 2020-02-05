<?php
/**
 * Catalog attribute property mapper
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\ResourceModel\Setup;

use Magento\Eav\Model\Entity\Setup\PropertyMapperAbstract;

class PropertyMapper extends PropertyMapperAbstract
{
    /**
     * Map input attribute properties to storage representation
     *
     * @param array $input
     * @param int $entityTypeId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map(array $input, $entityTypeId)
    {
        return [
            'is_used_in_profile_form' => $this->_getValue($input, 'used_in_profile_form', 0),
            'is_used_in_registration_form' => $this->_getValue($input, 'used_in_registration_form', 0),
            'hide_from_vendor_panel' => $this->_getValue($input, 'hide_from_vendor_panel', 0),
        ];
    }
}
