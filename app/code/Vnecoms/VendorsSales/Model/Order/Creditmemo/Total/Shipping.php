<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\Order\Creditmemo\Total;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

/**
 * Order creditmemo shipping total calculation model
 */
class Shipping extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Tax config
     *
     * @var \Magento\Tax\Model\Config
     */
    private $taxConfig;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($data);
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        return $this;
    }

    /**
     * Returns whether the user specified a shipping amount that already includes tax
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    private function isSuppliedShippingAmountInclTax($order)
    {
        // returns true if we are only displaying shipping including tax, otherwise returns false
        return $this->getTaxConfig()->displaySalesShippingInclTax($order->getStoreId());
    }

    /**
     * Get the Tax Config.
     * In a future release, will become a constructor parameter.
     *
     * @return \Magento\Tax\Model\Config
     *
     * @deprecated
     */
    private function getTaxConfig()
    {
        if ($this->taxConfig === null) {
            $this->taxConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Tax\Model\Config');
        }
        return $this->taxConfig;
    }
}
