<?php

namespace Simi\Simicustomize\Block\Adminhtml\Order\Totals;

class ServiceSupportFee extends \Magento\Framework\View\Element\Template
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        if ($order->getData('service_support_fee')) {
            $depositAmount = new \Magento\Framework\DataObject([
                'code' => 'service_support_fee',
                'label' => $this->getLabel(),
                'value' => $order->getData('service_support_fee'),
                'area' => '',
            ]);
            $this->getParentBlock()->addTotal($depositAmount, 'service_support_fee');
        }
        return $this;
    }

    public function getLabel(){
        return __('Service Support Fee');
    }
}
