<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Adminhtml\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;

/**
 * Adminhtml order creditmemo totals block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals
{
    /**
     * Creditmemo
     *
     * @var Creditmemo|null
     */
    protected $_creditmemo;
    

    /**
     * Get source
     *
     * @return Creditmemo|null
     */
    public function getSource()
    {
        return $this->getCreditmemo();
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

                $previewShippingAmount = 0 ;
                $previewBaseShippingAmount = 0 ;

                $isCreatedCreditmemoForShipping = false;


                foreach ($vendorOrder->getCreditmemoCollection() as $previousCreditmemo) {
                    $previewShippingTaxAmount += $previousCreditmemo->getShippingTaxAmount();
                    $previewBaseShippingTaxAmount += $previousCreditmemo->getBaseShippingTaxAmount();

                    if ((double)$previousCreditmemo->getShippingAmount()) {
                        $previewShippingAmount += $previousCreditmemo->getShippingAmount();
                        $previewBaseShippingAmount += $previousCreditmemo->getBaseShippingAmount();
                        // break;
                    }
                }

                // set shipping amount
                $vendorShippingAmount = $vendorOrder->getShippingAmount() - $previewShippingAmount > 0 ? $vendorOrder->getShippingAmount() - $previewShippingAmount
                    : 0;
                $baseVendorShippingAmount = $vendorOrder->getBaseShippingAmount() - $previewBaseShippingAmount > 0 ? $vendorOrder->getBaseShippingAmount() - $previewBaseShippingAmount
                        : 0;

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
                $oldShippingTaxAmount = $this->_creditmemo->getShippingTaxAmount();
                $oldBaseShippingTaxAmount = $this->_creditmemo->getBaseShippingTaxAmount();

                $this->_creditmemo->setShippingTaxAmount($vendorShippingTaxmount);
                $this->_creditmemo->setBaseShippingTaxAmount($baseVendorShippingTaxAmount);

                $this->_creditmemo->setTaxAmount($vendorTaxmount);
                $this->_creditmemo->setBaseTaxAmount($baseVendorTaxAmount);


                $grandTotal = $this->_creditmemo->getGrandTotal() - $oldShippingAmount - $oldShippingTaxAmount
                    + $vendorShippingAmount + $vendorShippingTaxmount
                ;
                $baseGrandTotal = $this->_creditmemo->getBaseGrandTotal() - $oldBaseShippingAmount - $oldBaseShippingTaxAmount
                    + $baseVendorShippingAmount + $baseVendorShippingTaxAmount;


                $this->_creditmemo->setGrandTotal($grandTotal);
                $this->_creditmemo->setBaseGrandTotal($baseGrandTotal);
            }
        }
        return $this->_creditmemo;
    }


    /**
     * Initialize order totals array
     *
     * @return $this

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
     */

    /**
     * Initialize creditmemo totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code' => 'adjustment_positive',
                    'value' => $this->getSource()->getAdjustmentPositive(),
                    'base_value' => $this->getSource()->getBaseAdjustmentPositive(),
                    'label' => __('Adjustment Refund'),
                ]
            )
        );
        $this->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code' => 'adjustment_negative',
                    'value' => $this->getSource()->getAdjustmentNegative(),
                    'base_value' => $this->getSource()->getBaseAdjustmentNegative(),
                    'label' => __('Adjustment Fee'),
                ]
            )
        );
        return $this;
    }
}
