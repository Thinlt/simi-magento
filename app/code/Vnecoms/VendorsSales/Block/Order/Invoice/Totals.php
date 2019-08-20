<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Order\Invoice;

use Vnecoms\VendorsSales\Model\Order;

class Totals extends \Vnecoms\VendorsSales\Block\Order\Totals
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @var Order|null
     */
    protected $_vendorInvoice = null;

    /**
     * @return Order
     */
    public function getVendorInvoice()
    {
        if ($this->_vendorInvoice === null) {
            if ($this->hasData('vendor_invoice')) {
                $this->_vendorInvoice = $this->_getData('vendor_invoice');
            } elseif ($this->_coreRegistry->registry('current_vendor_invoice')) {
                $this->_vendorInvoice = $this->_coreRegistry->registry('current_vendor_invoice');
            } elseif ($this->getParentBlock()->getVendorInvoice()) {
                $this->_vendorInvoice = $this->getParentBlock()->getVendorInvoice();
            }
        }
        return $this->_vendorInvoice;
    }

    /**
     * @param Order $invoice
     * @return $this
     */
    public function setVendorInvoice($invoice)
    {
        $this->_vendorInvoice = $invoice;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getSource()
    {
        return $this->getVendorInvoice();
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->removeTotal('base_grandtotal');
        return $this;
    }
}
