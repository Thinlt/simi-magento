<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShippingFlatRate\Model\Carrier;

use Vnecoms\VendorsShippingFlatRate\Model\Carrier\Flatrate\ItemPriceCalculator;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;

/**
 * Flat rate shipping model
 */
class Flatrate extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'vflatrate';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var ItemPriceCalculator
     */
    private $itemPriceCalculator;

    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_vendorConfig;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ItemPriceCalculator $itemPriceCalculator
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Vnecoms\VendorsShippingFlatRate\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator,
        \Vnecoms\VendorsConfig\Helper\Data $vendorConfig,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->itemPriceCalculator = $itemPriceCalculator;
        $this->_vendorConfig = $vendorConfig;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @param   string $vendor_id
     * @return  mixed
     */
    public function getVendorConfigData($field, $vendorId)
    {
        /**
         * field = {free_shipping_subtotal; price; type}
         */
        $path = 'shipping_method/flatrate/'.$field;
        return $this->_vendorConfig->getVendorConfig($path, $vendorId);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        /*The current method and multiple rate mehtod must to be both activated*/
        if (!$this->getConfigFlag('active') ||
            !$this->_scopeConfig->isSetFlag(
                'carriers/vendor_multirate/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->getStore()
            )
        ) {
            return false;
        }
        
        $om  = \Magento\Framework\App\ObjectManager::getInstance();
        $quotes = [];
        $vendorRates = [];
        /** @var Result $result */
        $result = $this->_rateResultFactory->create();
        
        /*loop through all items to get vendor id of each item*/
        
        /*
         * seperate each vendor item to dependence array in quotes array.
        */

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                $product    = $item->getProduct()->load($item->getProductId());
                if ($item->getParentItem() || $product->isVirtual()) {
                    continue;
                }
                
                if ($product->getVendorId()) {
                    if ($item->getVendorId()) {
                        $vendorId = $item->getVendorId();
                    } else {
                        $vendorId = $product->getVendorId();
                    }
                    $transport = new \Magento\Framework\DataObject(['vendor_id'=>$vendorId,'item'=>$item]);
                    $eventManager = $om->create('\Magento\Framework\Event\ManagerInterface');
                    
                    $eventManager->dispatch('ves_vendors_checkout_init_vendor_id', ['transport' => $transport]);
                    
                    $vendorId = $transport->getVendorId();
                        
                    /*Get all flatrate shipping info*/
                    if (!isset($vendorRates[$vendorId])) {
                        $vendorRates[$vendorId] = [];
                        $rates = $this->getVendorConfigData('rates', $vendorId);
                        if ($rates) {
                            $rates = unserialize($rates);
                            foreach ($rates as $rate) {
                                /* $identifier = preg_replace("/[^A-Za-z0-9 ]/", '', $rate['title']);
                                $identifier = str_replace(" ", "_", $identifier);
                                $identifier = strtolower($identifier); */
                                $identifier = $rate['identifier'];
                                $vendorRates[$vendorId][$identifier] = [
                                    'title'     => $rate['title'],
                                    'price'     => $rate['price'],
                                    'type'  => $rate['type'],
                                    'free_shipping'     => $rate['free_shipping'],
                                    'sort_order'    => $rate['sort_order'],
                                ];
                            }
                        }
                    }
                    
                    /*Get item by vendor id*/
                    if (!isset($quotes[$vendorId])) {
                        $quotes[$vendorId] = [];
                    }
                    $quotes[$vendorId][] = $item;
                } else {
                    $quotes['no_vendor'][] = $item;
                }
            }
            
            ksort($vendorRates);

            /*Add shipping method for each vendor flatrate*/
            foreach ($vendorRates as $vendorId => $rates) {
                if (!$this->getVendorConfigData('active', $vendorId)) {
                    continue;
                }
                $total  = 0;
                foreach ($quotes[$vendorId] as $item) {
                    $product    = $item->getProduct()->load($item->getProductId());
                    if ($item->getParentItem() || $product->isVirtual()) {
                        continue;
                    }
                    $total += $item->getRowTotal();
                }
            
                foreach ($rates as $code => $rate) {
                    /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                    $method = $this->_rateMethodFactory->create();
                    
                    $method->setCarrier($this->_code);
                    $method->setVendorId($vendorId);
                    $method->setCarrierTitle($this->getConfigData('title'));
                    
                    $method->setMethod($code.\Vnecoms\VendorsShipping\Plugin\Shipping::SEPARATOR.$vendorId);
                    $method->setMethodTitle($rate['title']);
                    
                    if ($rate['type'] == 'O') {       //per order
                        $shippingPrice = $rate['price'];
                    } else {
                        $shippingPrice  = 0;
                        $qty            = 0;
                        foreach ($quotes[$vendorId] as $item) {
                            $product    = $item->getProduct();
                            if ($product->isVirtual() || $item->getParentItem()) {
                                continue;
                            }
                            if ($item->getFreeShipping()) {
                                continue;
                            }
                            $qty += $item->getQty();
                        }
                        $shippingPrice = $qty * $rate['price'];
                    }
                     
                    if ($rate['free_shipping'] && $total >= $rate['free_shipping']) {
                        $shippingPrice = 0;
                    }
                    
                    $vendorRates[$vendorId][$code]['shipping_price'] = $shippingPrice;
                    $method->setPrice($shippingPrice);
                    $method->setCost($shippingPrice);
                    $result->append($method);
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['vflatrate' => $this->getConfigData('name')];
    }
}
