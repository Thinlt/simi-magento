<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Model\Source;

use Vnecoms\VendorsCredit\Model\Withdrawal;

class Status extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{


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
                ['label' => __('Pending'), 'value' => Withdrawal::STATUS_PENDING],
                ['label' => __('Completed'), 'value' => Withdrawal::STATUS_COMPLETED],
                ['label' => __('Canceled'), 'value' => Withdrawal::STATUS_CANCELED],
                /* ['label' => __('Rejected'), 'value' => Withdrawal::STATUS_REJECTED], */
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
}
