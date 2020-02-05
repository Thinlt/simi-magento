<?php
/**
 * Copyright 2019 Simi. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\Simicustomize\Model\Source\Reserve;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Simi\Simicustomize\Model\Source\Reserve\Status
 *
 * @package Simi\Simicustomize\Model\Source\Reserve\Status
 */
class Status implements OptionSourceInterface
{
    protected $_options;

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $config;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ){
        
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $config = $this->config->getValue('sales/reserve/state');
            $states = explode(',', $config);
            foreach($states as $type){
                $type = trim($type);
                $this->_options[] = ['label' => __($type), 'value' => $type];
            }
        }
        return $this->_options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
