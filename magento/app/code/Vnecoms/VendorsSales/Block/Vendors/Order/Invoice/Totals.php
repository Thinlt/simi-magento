<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\Invoice;

use Magento\Sales\Model\Order\Invoice;

/**
 * Adminhtml order invoice totals block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    /**
     * @var \Vnecoms\VendorsSales\Model\Order\Invoice
     */
    protected $_vendorInvoice;
    
    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        if (isset($this->_totals['shipping'])) {
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
     * Get vendor invoice.
     * @return \Vnecoms\VendorsSales\Model\Order\Invoice
     */
    public function getVendorInvoice()
    {
        if (!$this->_vendorInvoice) {
            if ($invoice = $this->_coreRegistry->registry('vendor_invoice')) {
                $this->_vendorInvoice = $invoice;
            } else {
                $om = \Magento\Framework\App\ObjectManager::getInstance();
                $this->_vendorInvoice = $om->create('Vnecoms\VendorsSales\Model\Order\Invoice');


                //var_dump($this->getInvoice()->getData());exit;
                $this->_vendorInvoice->setData($this->getInvoice()->getData());
                $vendorOrder = $this->_coreRegistry->registry('vendor_order');
                $this->_vendorInvoice->setVendorOrderId($vendorOrder->getId());

                $previewTaxAmount = 0 ;
                $previewBaseTaxAmount = 0 ;
                $previewShippingTaxAmount = 0 ;
                $previewBaseShippingTaxAmount = 0 ;

                $isCreatedInvoiceForShipping = false;
                foreach ($vendorOrder->getInvoiceCollection() as $previousInvoice) {
                    $previewShippingTaxAmount += $previousInvoice->getShippingTaxAmount();
                    $previewBaseShippingTaxAmount += $previousInvoice->getBaseShippingTaxAmount();
                  //  $previewTaxAmount += $previousInvoice->getTaxAmount();
                  //  $previewBaseTaxAmount += $previousInvoice->getBaseTaxAmount();
                    if ((double)$previousInvoice->getShippingAmount() && !$previousInvoice->isCanceled()) {
                        $isCreatedInvoiceForShipping = true;
                    }
                }

                // shipping amount
                $vendorShippingAmount = $isCreatedInvoiceForShipping?0:$vendorOrder->getShippingAmount();
                $baseVendorShippingAmount = $isCreatedInvoiceForShipping?0:$vendorOrder->getBaseShippingAmount();


                $this->_vendorInvoice->setShippingAmount($vendorShippingAmount);
                $this->_vendorInvoice->setBaseShippingAmount($baseVendorShippingAmount);

                // shipping tax amount
                $vendorShippingTaxmount = $vendorOrder->getShippingTaxAmount() - $previewShippingTaxAmount > 0 ?
                    $vendorOrder->getShippingTaxAmount() - $previewShippingTaxAmount : 0;
                $baseVendorShippingTaxAmount = $vendorOrder->getBaseShippingTaxAmount() - $previewBaseShippingTaxAmount > 0 ?
                    $vendorOrder->getBaseShippingTaxAmount() - $previewBaseShippingTaxAmount : 0;
                ;

                $vendorTaxmount = $this->_vendorInvoice->getTaxAmount() - $this->_vendorInvoice->getShippingTaxAmount()
                                    +$vendorShippingTaxmount;


                $baseVendorTaxAmount = $this->_vendorInvoice->getBaseTaxAmount() - $this->_vendorInvoice->getBaseShippingTaxAmount()
                    +$baseVendorShippingTaxAmount;
                ;

                $this->_vendorInvoice->setShippingTaxAmount($vendorShippingTaxmount);
                $this->_vendorInvoice->setBaseShippingTaxAmount($baseVendorShippingTaxAmount);

                /*
                // tax amount
                $vendorTaxmount = $vendorOrder->getTaxAmount() - $previewTaxAmount > 0 ?
                    $vendorOrder->getTaxAmount() - $previewTaxAmount : 0;
                $baseVendorTaxAmount = $vendorOrder->getBaseTaxAmount() - $previewBaseTaxAmount > 0 ?
                    $vendorOrder->getBaseTaxAmount() - $previewBaseTaxAmount : 0;
                ;
                  */
                $this->_vendorInvoice->setTaxAmount($vendorTaxmount);
                $this->_vendorInvoice->setBaseTaxAmount($baseVendorTaxAmount);


                // set grandtotal and base grantotal for invoice
                $grandTotal = $this->getInvoice()->getGrandTotal()
                    - $this->getInvoice()->getShippingAmount() - $this->getInvoice()->getShippingTaxAmount()
                    + $vendorShippingAmount + $vendorShippingTaxmount ;
                $baseGrandTotal = $this->getInvoice()->getBaseGrandTotal() - $this->getInvoice()->getBaseShippingAmount()
                        - $this->getInvoice()->getBaseShippingTaxAmount()
                    + $baseVendorShippingAmount + $baseVendorShippingTaxAmount ;
                
                $this->_vendorInvoice->setGrandTotal($grandTotal);
                $this->_vendorInvoice->setBaseGrandTotal($baseGrandTotal);
            }
        }
        return $this->_vendorInvoice;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals::getSource()
     */
    public function getSource()
    {
        return $this->getVendorInvoice();
    }
}
