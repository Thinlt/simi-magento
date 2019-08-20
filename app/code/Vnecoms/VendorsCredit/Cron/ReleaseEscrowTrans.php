<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Cron;

use Vnecoms\VendorsCredit\Model\ResourceModel\Escrow\CollectionFactory;

class ReleaseEscrowTrans
{
    /**
     * @var \Vnecoms\VendorsCredit\Model\ResourceModel\Escrow\CollectionFactory
     */
    protected $_collectionFactory;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    
    /**
     * @var \Vnecoms\VendorsCredit\Helper\Data
     */
    protected $_creditHelper;
    
    public function __construct(
        CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Vnecoms\VendorsCredit\Helper\Data $creditHelper
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->_creditHelper = $creditHelper;
    }
    
    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function execute()
    {
        $this->_logger->log(\Psr\Log\LogLevel::INFO, __("Escrow cron is ran"));
        
        $collection = $this->_collectionFactory->create();
        $today = (new \DateTime())->getTimestamp();
        $holdTimeDay = $this->_creditHelper->getHoldTimeDays();
        $holdTimeDay = $holdTimeDay?$holdTimeDay:0;
        
        $date = date('Y-m-d', strtotime('-'.$holdTimeDay.' days', $today));
       
        $collection->addFieldToFilter('status', \Vnecoms\VendorsCredit\Model\Escrow::STATUS_PENDING)
            ->addFieldToFilter('created_at', ['lt' => $date]);
        
        $this->_logger->log(\Psr\Log\LogLevel::INFO, 'Number of transactions will be processed: '.$collection->count());
        
        try {
            foreach ($collection as $escrow) {
                $escrow->release();
                $this->_logger->log(\Psr\Log\LogLevel::INFO, __("Escrow transaction #%1 is released", $escrow->getId()));
            }
        } catch (\Exception $e) {
            $this->_logger->log(\Psr\Log\LogLevel::ERROR, $e->getMessage());
        }
    }
}
