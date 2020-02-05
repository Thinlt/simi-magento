<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductAttribute implements ObserverInterface
{
    /**
     * @var \Vnecoms\VendorsProduct\Model\Source\Approval
     */
    protected $_approvalOption;
    
    public function __construct(
        \Vnecoms\VendorsProduct\Model\Source\Approval $approvalOptions
    ) {
        $this->_approvalOption = $approvalOptions;
    }
    /**
     * Modify no Cookies forward object
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        return $this;
    }
}
