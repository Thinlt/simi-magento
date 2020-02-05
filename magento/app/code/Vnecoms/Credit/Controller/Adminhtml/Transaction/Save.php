<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Controller\Adminhtml\Transaction;

use Vnecoms\Credit\Controller\Adminhtml\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Vnecoms\Credit\Model\Processor\AdminAddCredit;
use Vnecoms\Credit\Model\Processor\AdminSubtractCredit;

class Save extends Action
{
    /**
     * @var \Vnecoms\Credit\Model\Processor
     */
    protected $creditProcessor;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $transactionFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $creditAccountFactory;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        \Vnecoms\Credit\Model\Processor $creditProcessor,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context,$coreRegistry, $dateFilter);
        $this->_storeManager        = $storeManager;
        $this->resultJsonFactory    = $resultJsonFactory;
        $this->transactionFactory   = $transactionFactory;
        $this->creditAccountFactory = $creditAccountFactory;
        $this->creditProcessor      = $creditProcessor;
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        try{
            $request    = $this->getRequest();
            $response   = new \Magento\Framework\DataObject();
            
            $creditAmount   = $request->getParam('credit',0);
            $customerId     = $request->getParam('customer_id');
            $description    = $request->getParam('description');
            $creditAccount  = $this->creditAccountFactory->create();
            $creditAccount->loadByCustomerId($customerId);
            
            $type = $creditAmount > 0?AdminAddCredit::TYPE:AdminSubtractCredit::TYPE;
            
            $data = array(
                'type'		=> $type,
                'amount'	=> abs($creditAmount),
                'description' => $description
            );
        
            $this->creditProcessor->process($creditAccount,$data);
            $creditAccount->load($creditAccount->getId());
            $response->setData([
                'error' => false,
                'credit_balance' => $this->_formatCredit($creditAccount->getCredit()),
                'changed_credit' => $creditAmount,
            ]);
            return $this->resultJsonFactory->create()->setJsonData($response->toJson());
        }catch (\Exception $e){
            $response->setData(['error'=>true, 'msg'=>$e->getMessage()] );
            return $this->resultJsonFactory->create()->setJsonData($response->toJson());
        }
    }
    
    /**
     * Format Credit
     * @param float $credit
     */
    protected function _formatCredit($credit){
        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency();
        return $baseCurrency->formatPrecision($credit,2,[],false);
    }
}
