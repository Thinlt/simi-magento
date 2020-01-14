<?php
/**
 * Copyright 2019 Simi. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\Simicustomize\Model\Source\Service;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Simi\Simicustomize\Model\Source\Service\ServiceType
 *
 * @package Simi\Simicustomize\Model\Source\Service
 */
class ServiceType implements OptionSourceInterface
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
            $config = $this->config->getValue('sales/service/types');
            $services = explode(',', $config);
            foreach($services as $type){
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
