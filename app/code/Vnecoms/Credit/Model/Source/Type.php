<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Source;

use Magento\Framework\DB\Ddl\Table;

class Type extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const TYPE_FIXED    = 1;
    const TYPE_OPTION   = 2;
    const TYPE_RANGE    = 3;
    
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Fixed value'), 'value' => self::TYPE_FIXED],
                [
                    'label' => __('Dropdown values'), 
                    'value' => self::TYPE_OPTION
                ],
                ['label' => __('Custom value'), 'value' => self::TYPE_RANGE],
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
    
    
    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
    
    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
    
        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Credit Type',
            ],
        ];
    }

}
