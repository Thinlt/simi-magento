<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Checkout;

use Magento\Checkout\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Api\StoreResolverInterface;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->hasCreditItem()) {
            unset($jsLayout['components']['checkout']['children']
                ['sidebar']['children']['summary']['children']['totals']['children']['credit']);

            unset($jsLayout['components']['checkout']['children']
                ['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['credit']);
        }
        return $jsLayout;
    }

    /**
     * has credit item
     * @return bool
     */
    public function hasCreditItem(){
        $isCredit = false;
        $qoute = $this->_checkoutSession->getQuote();
        foreach($qoute->getAllItems() as $item) {
            if($item->getProductType() == \Vnecoms\Credit\Model\Product\Type\Credit::TYPE_CODE){
                $isCredit = true;
                break;
            }
        }
        return $isCredit;
    }
}