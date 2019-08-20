<?php
namespace Vnecoms\VendorsCredit\Model;

class Withdrawal extends \Magento\Framework\Model\AbstractModel
{

    const ENTITY = 'vendor_withdrawal';
    
    const STATUS_CANCELED = 0;
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_REJECTED = 3;
    
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'vendor_withdrawal';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'vendor_withdrawal';

    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Vnecoms\VendorsCredit\Model\ResourceModel\Withdrawal');
    }
    
    /**
     * Can cancel
     *
     * @return boolean
     */
    public function canCancel()
    {
        return $this->getStatus() == self::STATUS_PENDING;
    }
    
    /**
     * Cancel The Withdrawal
     *
     * @throws \Exception
     * @return \Vnecoms\VendorsCredit\Model\Withdrawal
     */
    public function cancel()
    {
        if (!$this->canCancel()) {
            throw new \Exception(__("Can not cancel this withdrawal request."));
        }
        
        $this->setStatus(self::STATUS_CANCELED)->save();
        return $this;
    }
    
    /**
     * Can complete the request
     *
     * @return boolean
     */
    public function canComplete()
    {
        return $this->canCancel();
    }
    
    /**
     * Mark the request as complete
     * @throws \Exception
     * @return \Vnecoms\VendorsCredit\Model\Withdrawal
     */
    public function markAsComplete()
    {
        if (!$this->canComplete()) {
            throw new \Exception(__("Can not complete this withdrawal request."));
        }
        
        $this->setStatus(self::STATUS_COMPLETED)->save();
        return $this;
    }
}
