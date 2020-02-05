<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCommission\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsCommission\Model\Rule as CommissionRule;

class CalculateCommission implements ObserverInterface
{
    /**
     * @var \Vnecoms\VendorsCommission\Model\Rule
     */
    protected $_ruleFactory;
    
    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $_catalogRuleFactory;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Vnecoms\VendorsCommission\Model\RuleFactory $ruleFactory,
        \Vnecoms\VendorsCommission\Model\TmpRuleFactory $catalogRuleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->_catalogRuleFactory = $catalogRuleFactory;
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
    }
    
    /**
     * Calculate commission.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $commissionObj = $observer->getCommission();
        $invoiceItem   = $observer->getInvoiceItem();
        $product       = $observer->getProduct();
        $vendor        = $observer->getVendor();
        $vendorGroupId = $vendor->getGroupId();
        $invoice       = $invoiceItem->getInvoice();
        $storeId       = $invoice->getStoreId();
        $websiteId     = $this->_storeManager->getStore($storeId)->getWebsiteId();
         
        $ruleCollection = $this->_ruleFactory->create()->getCollection()
            ->addFieldToFilter('vendor_group_ids', ['finset'=>$vendorGroupId])
            ->addFieldToFilter('website_ids', ['finset'=>$websiteId])
            ->addFieldToFilter('is_active', CommissionRule::STATUS_ENABLED);
         
        $today = (new \DateTime())->format('Y-m-d');
        $ruleCollection->getSelect()
            ->where(
                '(from_date IS NULL OR from_date<=?) AND (to_date IS NULL OR to_date>=?)',
                $today,
                $today
            )->order('priority ASC');

        if ($ruleCollection->count()) {
            $ruleDescriptionArr = [];
            $fee = $commissionObj->getFee();

            foreach ($ruleCollection as $rule) {
                $tmpRule = $this->_catalogRuleFactory->create();
                /*If the product is not match with the conditions just continue*/
                $tmpRule->setConditionsSerialized($rule->getConditionSerialized());
                if (!$tmpRule->getConditions()->validate($product)) {
                    continue;
                }
                $tmpFee = 0;
                switch ($rule->getData('commission_by')) {
                    case CommissionRule::COMMISSION_BY_FIXED_AMOUNT:
                        $tmpFee = $rule->getData('commission_amount');
                        break;
                    case CommissionRule::COMMISSION_BY_PERCENT_PRODUCT_PRICE:
                        if (!$invoiceItem->getData('base_row_total')) {
                            $baseRowTotal = ($invoiceItem->getData('price_incl_tax') * $invoiceItem->getData('qty')) - $invoiceItem->getData('base_tax_amount');
                            $invoiceItem->setData('base_row_total', $baseRowTotal);
                        }
                        switch ($rule->getData('commission_action')) {
                            case CommissionRule::COMMISSION_BASED_PRICE_INCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total') + $invoiceItem->getData('base_tax_amount');
                                break;
                            case CommissionRule::COMMISSION_BASED_PRICE_EXCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total');
                                break;
                            case CommissionRule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_INCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total') - $invoiceItem->getData('base_discount_amount') + $invoiceItem->getData('base_tax_amount');
                                break;
                            case CommissionRule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_EXCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total')  - $invoiceItem->getData('base_discount_amount');
                                break;
                            default:
                                $amount = $invoiceItem->getData('base_row_total')  - $invoiceItem->getData('base_discount_amount');
                        }
                        $tmpFee = ($rule->getData('commission_amount') * $amount)/100;
                        break;
                }
                $tmpFeeWithCurrency = $invoice->getOrder()->formatBasePrice($tmpFee);
        
                $ruleDescriptionArr[] = $rule->getDescription().": -".$tmpFeeWithCurrency;
        
                $fee +=  $tmpFee;
        
                /*Break if the flag stop rules processing is set to 1*/
                if ($rule->getData('stop_rules_processing')) {
                    break;
                }
            }
            $commissionObj->setFee($fee);
            $commissionObj->setDescriptions($ruleDescriptionArr);
        }
        return $this;
    }
}
