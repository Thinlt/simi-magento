<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\Totals;

/**
 * Adminhtml order tax totals block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Tax\Block\Sales\Order\Tax
{
    /**
     * Tax helper
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelper;

    /**
     * Tax calculation
     *
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxCalculation;

    /**
     * Tax factory
     *
     * @var \Magento\Tax\Model\Sales\Order\TaxFactory
     */
    protected $_taxOrderFactory;

    /**
     * Sales admin helper
     *
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_salesAdminHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\Sales\Order\TaxFactory $taxOrderFactory
     * @param \Magento\Sales\Helper\Admin $salesAdminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\Sales\Order\TaxFactory $taxOrderFactory,
        \Magento\Sales\Helper\Admin $salesAdminHelper,
        array $data = []
    ) {
        $this->_taxHelper = $taxHelper;
        $this->_taxCalculation = $taxCalculation;
        $this->_taxOrderFactory = $taxOrderFactory;
        $this->_salesAdminHelper = $salesAdminHelper;
        parent::__construct($context, $taxConfig, $data);
    }

    /**
     * Display tax amount
     *
     * @param string $amount
     * @param string $baseAmount
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        $source = $this->getSource();
        if ($source instanceof \Vnecoms\VendorsSales\Model\Order\Invoice) {
            $source = $source->getInvoice();
        }
        return $this->_salesAdminHelper->displayPrices($source, $baseAmount, $amount, false, '<br />');
    }

    /**
     * Get store object for process configuration settings
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
}
