<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Source\Transaction;

use Magento\Framework\DB\Ddl\Table;

class Type extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Vnecoms\Credit\Model\Processor
     */
    protected $_creditProcessor;
    
    public function __construct(\Vnecoms\Credit\Model\Processor $creditProcessor){
        $this->_creditProcessor = $creditProcessor;
    }
    
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
            foreach ($this->_creditProcessor->getProcessors() as $processor){
                $this->_options[] = [
                    'label' => $processor->getTitle(),
                    'value' => $processor->getCode()
                ];
            }
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

}
