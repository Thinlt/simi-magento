<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model;

/**
 * Customer attribute model
 *
 * @method int getSortOrder()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Attribute extends \Magento\Eav\Model\Entity\Attribute
{
    /**
     * Name of the module
     */
    const MODULE_NAME = 'Vnecoms_Vendors';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'vendor_entity_attribute';

    /**
     * Prefix of model events object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';


    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Vendors\Model\ResourceModel\Attribute');
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Eav\Model\Entity\Attribute::__sleep()
     */
    public function __sleep()
    {
        $this->unsetData('entity_type');
        return parent::__sleep();
    }
}
