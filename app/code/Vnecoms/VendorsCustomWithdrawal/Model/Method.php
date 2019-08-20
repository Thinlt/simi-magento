<?php
namespace Vnecoms\VendorsCustomWithdrawal\Model;

class Method extends \Magento\Framework\Model\AbstractModel
{
    const FEE_TYPE_FIXED    = 'fixed';
    const FEE_TYPE_PERCENT  = 'percent';

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'withdrawal_method';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'method';
    
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method');
    }
}
