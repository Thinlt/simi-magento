<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsConfig\Helper\Data as VendorConfig;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Psr\Log\LoggerInterface;

class UpdateShippingInfo implements ObserverInterface
{
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_vendorConfig;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;
    
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Tax module helper
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManage;
    
    /**
     * @var \Vnecoms\VendorsShipping\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Vnecoms\Vendors\Helper\Data $vendorHelper
     * @param \Vnecoms\VendorsShipping\Helper\Data $helper
     * @param PriceCurrencyInterface $priceCurrency
     * @param VendorConfig $vendorConfig
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Module\Manager $moduleManage
     */
    public function __construct(
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Vnecoms\VendorsShipping\Helper\Data $helper,
        PriceCurrencyInterface $priceCurrency,
        VendorConfig $vendorConfig,
        LoggerInterface $logger,
        \Magento\Framework\Module\Manager $moduleManage
    ) {
        $this->_vendorHelper    = $vendorHelper;
        $this->helper           = $helper;
        $this->_priceCurrency   = $priceCurrency;
        $this->_vendorConfig    = $vendorConfig;
        $this->logger           = $logger;
        $this->_moduleManage    = $moduleManage;
    }
    
    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* Do nothing if the extension is not enabled.*/
        if (!$this->_vendorHelper->moduleEnabled() || !$this->helper->isEnabled()) {
            return;
        }
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        $vendorOrderData = $observer->getOrderData();
        $vendorId = $observer->getVendorId();
        
        if ($quote->isVirtual()) {
            return;
        }

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        $shippingMethod = str_replace('vendor_multirate_', '', $shippingMethod);
        $shippingMethods = explode(
            \Vnecoms\VendorsShipping\Plugin\Shipping::METHOD_SEPARATOR,
            $shippingMethod
        );
        
        $vendorShippingMethod = false;
        foreach ($shippingMethods as $method) {
            $tmpMethods = explode(
                \Vnecoms\VendorsShipping\Plugin\Shipping::SEPARATOR,
                $method
            );
            
            if (sizeof($tmpMethods) != 2) {
                continue;
            }
            if ($vendorId == $tmpMethods[1]) {
                $vendorShippingMethod = $method;
            }
        }
        
        if (!$vendorShippingMethod) {
            return;
        }
        $shippingRate = $quote->getShippingAddress()->getShippingRateByCode($vendorShippingMethod);
        
        if (!$shippingRate || !$shippingRate->getId()) {
            return;
        }
        $vendorOrderData->setData('shipping_description', $shippingRate->getCarrierTitle().' - '.$shippingRate->getMethodTitle());
        $vendorOrderData->setData('shipping_method', $shippingRate->getCode());
        $vendorOrderData->setData('base_shipping_amount', $shippingRate->getPrice());
        $vendorOrderData->setData('shipping_amount', $this->_priceCurrency->convert($shippingRate->getPrice(), null, $order->getData('order_currency_code')));

        $vendorOrderData->setData('shipping_incl_tax', $this->_priceCurrency->convert($shippingRate->getPrice() + $vendorOrderData->getData("shipping_tax_amount") , null, $order->getData('order_currency_code')));
        $vendorOrderData->setData('base_shipping_incl_tax', $shippingRate->getPrice() + $vendorOrderData->getData("base_shipping_tax_amount"));

        if ($this->_moduleManage->isEnabled("Vnecoms_VendorsTax")) {
            $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
            $itemTaxs = $object_manager->get('\Vnecoms\VendorsTax\Model\ResourceModel\Order\Tax\Item')
                ->getShipTaxItemsByOrderIdAndVendorId($order->getId(), $vendorId);
            $shippingTaxAmount = 0;
            $baseShippingTaxAmount = 0;
            foreach ($itemTaxs as $tax) {
                $shippingTaxAmount += $tax["real_amount"];
                $baseShippingTaxAmount += $tax["real_base_amount"];
            }
            $vendorOrderData->setData('shipping_tax_amount', $shippingTaxAmount);
            $vendorOrderData->setData('base_shipping_tax_amount', $baseShippingTaxAmount);

            $shippingPriceIncludesTax =  $object_manager->get('\Magento\Tax\Model\Config')
                ->shippingPriceIncludesTax($order->getStore());

            if (!$shippingPriceIncludesTax) {
                $vendorOrderData->setData('shipping_incl_tax', $vendorOrderData->getData("shipping_amount") + $shippingTaxAmount);
                $vendorOrderData->setData('base_shipping_incl_tax', $vendorOrderData->getData("base_shipping_amount") + $baseShippingTaxAmount);
            } else {
                $vendorOrderData->setData('shipping_incl_tax', $vendorOrderData->getData("shipping_amount"));
                $vendorOrderData->setData('base_shipping_incl_tax', $vendorOrderData->getData("base_shipping_amount"));

                $vendorOrderData->setData('shipping_amount', $vendorOrderData->getData("shipping_amount") - $shippingTaxAmount);
                $vendorOrderData->setData('base_shipping_amount', $vendorOrderData->getData("base_shipping_amount") - $baseShippingTaxAmount);
            }

            $vendorOrderData->setData('tax_amount', $vendorOrderData->getData("tax_amount") + $shippingTaxAmount);
            $vendorOrderData->setData('base_tax_amount', $vendorOrderData->getData("base_tax_amount") + $baseShippingTaxAmount);
        }

        return $this;
    }
}
