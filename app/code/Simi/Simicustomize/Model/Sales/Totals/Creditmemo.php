<?php

namespace Simi\Simicustomize\Model\Sales\Totals;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo as ModelCreditmemo;

/**
* Class Custom
* @package Simi\Simicustomize\Model\Total\Quote
*/
class Creditmemo extends AbstractTotal
{
   /**
     * @param ModelCreditmemo $creditmemo
     * @return $this
     */
    public function collect(ModelCreditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        $order = $creditmemo->getOrder();
        if ($order->getOrderType() == 'pre_order') {
            $baseGrandTotal = $order->getBaseGrandTotal();
            $grandTotal = $order->getGrandTotal();
            $creditmemo->setBaseGrandTotal($baseGrandTotal);
            $creditmemo->setGrandTotal($grandTotal);
        }
        return $this;
    }
}