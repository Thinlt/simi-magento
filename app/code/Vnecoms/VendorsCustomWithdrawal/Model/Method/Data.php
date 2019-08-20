<?php
namespace Vnecoms\VendorsCustomWithdrawal\Model\Method;

class Data extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'withdrawal_method_data';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'method_data';
    
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\Data');
    }
}
