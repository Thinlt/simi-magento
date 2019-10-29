<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Block\Adminhtml\Order\Totals;

/**
 * Adminhtml order tax totals block
 *
 * @api
 * @author      SimiCart Team <support@simicart.com>
 */
class PreorderDeposit extends \Magento\Framework\View\Element\Template
{
    /**
     * Initialize all order totals with Pre-order reposit
     *
     * @return \Simi\Simicustomize\Block\Adminhtml\Order\Totals\PreorderDeposit
     */
    public function initTotals()
    {
        $depositAmount = new \Magento\Framework\DataObject([
            'code' => 'preorder_deposit',
            // 'block_name' => $this->getNameInLayout(),
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'area' => 'footer',
        ]);
        $this->getParentBlock()->addTotal($depositAmount, 'paid');
        return $this;
    }

    public function getLabel(){
        return __(\Simi\Simicustomize\Model\Total\Quote\Preorder::LABEL);
    }

    public function getValue(){
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        return $order->getBaseDepositAmount();
    }
}
