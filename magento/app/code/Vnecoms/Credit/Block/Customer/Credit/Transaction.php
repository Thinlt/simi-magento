<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Customer\Credit;

/**
 * Shopping cart item render block for configurable products.
 */
class Transaction extends \Vnecoms\Credit\Block\Customer\Credit
{
    protected $_transactions;
    
    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getTransactions()
    {
        if(!$this->_transactions){
            $this->_transactions = $this->getCreditAccount()->getTransactionCollection()
            ->addOrder('transaction_id','desc');
        }
        return $this->_transactions;
    }
    
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTransactions()->count()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'customer.credit.transactions.pager'
            )->setCollection(
                $this->getTransactions()
            );
            $this->setChild('pager', $pager);
            $this->getTransactions()->load();
        }
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * Get transaction type by code.
     * @param string $typeCode
     * @return string
     */
    public function getTransactionType($typeCode){
        return $this->creditProcessor->getProcessor($typeCode)->getTitle();
    }
    
    /**
     * Get transaction description
     * @param \Vnecoms\Credit\Model\Credit\Transaction $transaction
     * @return string
     */
    public function getDescription(\Vnecoms\Credit\Model\Credit\Transaction $transaction){
        return $this->creditProcessor->getProcessor($transaction->getType())->getDescription($transaction);
    }
}
