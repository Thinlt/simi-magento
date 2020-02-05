<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;

/**
 * Adminhtml order invoice totals block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
{
    /**
     * @var Creditmemo
     */
    protected $_creditmemo;

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        if (!$this->getSource()->getIsVirtual() && ((double)$this->getSource()->getShippingAmount() ||
                $this->getSource()->getShippingDescription())
        ) {
            $this->_totals['shipping'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping',
                    'value' => $this->getSource()->getShippingAmount(),
                    'base_value' => $this->getSource()->getBaseShippingAmount(),
                    'label' => __('Shipping & Handling'),
                ]
            );
        }
        return $this;
    }


    /**
     * Retrieve creditmemo model instance
     *
     * @return Creditmemo
     */
    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->hasData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif ($this->_coreRegistry->registry('current_creditmemo')) {
                $this->_creditmemo = $this->_coreRegistry->registry('current_creditmemo');
            } elseif ($this->getParentBlock() && $this->getParentBlock()->getCreditmemo()) {
                $this->_creditmemo = $this->getParentBlock()->getCreditmemo();
            }

            if (!$this->_creditmemo->getId()) {
                $vendorOrder = $this->_coreRegistry->registry('vendor_order');
                //echo $vendorOrder->getId();exit;
                $previewTaxAmount = 0 ;
                $previewBaseTaxAmount = 0 ;
                $previewShippingTaxAmount = 0 ;
                $previewBaseShippingTaxAmount = 0 ;

                $isCreatedCreditmemoForShipping = false;


                foreach ($vendorOrder->getCreditmemoCollection() as $previousCreditmemo) {
                    $previewShippingTaxAmount += $previousCreditmemo->getShippingTaxAmount();
                    $previewBaseShippingTaxAmount += $previousCreditmemo->getBaseShippingTaxAmount();

                    if ((double)$previousCreditmemo->getShippingAmount()) {
                        $isCreatedCreditmemoForShipping = true;
                        // break;
                    }
                }

                // set shipping amount
                $vendorShippingAmount = $isCreatedCreditmemoForShipping ? 0 : $vendorOrder->getShippingAmount();
                $baseVendorShippingAmount = $isCreatedCreditmemoForShipping ? 0 : $vendorOrder->getBaseShippingAmount();

                $oldShippingAmount = $this->_creditmemo->getShippingAmount();
                $oldBaseShippingAmount = $this->_creditmemo->getBaseShippingAmount();

                $this->_creditmemo->setShippingAmount($vendorShippingAmount);
                $this->_creditmemo->setBaseShippingAmount($baseVendorShippingAmount);

                // set shipping tax amount
                $vendorShippingTaxmount = $vendorOrder->getShippingTaxAmount() - $previewShippingTaxAmount > 0 ?
                    $vendorOrder->getShippingTaxAmount() - $previewShippingTaxAmount : 0;
                $baseVendorShippingTaxAmount = $vendorOrder->getBaseShippingTaxAmount() - $previewBaseShippingTaxAmount > 0 ?
                    $vendorOrder->getBaseShippingTaxAmount() - $previewBaseShippingTaxAmount : 0;
                ;
                $vendorTaxmount = $this->_creditmemo->getTaxAmount() - $this->_creditmemo->getShippingTaxAmount()
                    +$vendorShippingTaxmount;
                $baseVendorTaxAmount = $this->_creditmemo->getBaseTaxAmount() - $this->_creditmemo->getBaseShippingTaxAmount()
                    +$baseVendorShippingTaxAmount;
                ;

                $this->_creditmemo->setShippingTaxAmount($vendorShippingTaxmount);
                $this->_creditmemo->setBaseShippingTaxAmount($baseVendorShippingTaxAmount);

                $this->_creditmemo->setTaxAmount($vendorTaxmount);
                $this->_creditmemo->setBaseTaxAmount($baseVendorTaxAmount);


                $grandTotal = $this->_creditmemo->getGrandTotal() - $oldShippingAmount - $this->_creditmemo->getShippingTaxAmount()
                    + $vendorShippingAmount + $vendorShippingTaxmount
                ;
                $baseGrandTotal = $this->_creditmemo->getBaseGrandTotal() - $oldBaseShippingAmount - $this->_creditmemo->getBaseShippingTaxAmount()
                    + $baseVendorShippingAmount + $baseVendorShippingTaxAmount;


                $this->_creditmemo->setGrandTotal($grandTotal);
                $this->_creditmemo->setBaseGrandTotal($baseGrandTotal);
            }
        }
        return $this->_creditmemo;
    }
}
