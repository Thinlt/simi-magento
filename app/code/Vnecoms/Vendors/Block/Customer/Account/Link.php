<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Customer\Account;

use Vnecoms\Vendors\Model\Source\PanelType;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Shopping cart item render block for configurable products.
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;

    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        array $data = []
    ) {
        $this->_vendorHelper = $vendorHelper;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        if ($this->_vendorHelper->getPanelType() == PanelType::TYPE_ADVANCED) {
            return '';
        }
        return parent::_toHtml();
    }
}
