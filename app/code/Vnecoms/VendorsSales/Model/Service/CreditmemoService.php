<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsSales\Model\Service;

/**
 * Class CreditmemoService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreditmemoService extends \Magento\Sales\Model\Service\CreditmemoService
{



    /**
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateForRefund(\Magento\Sales\Api\Data\CreditmemoInterface $creditmemo)
    {

        if ($creditmemo->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We cannot register an existing credit memo.')
            );
        }
        
        
        if ($creditmemo->getVendorOrder() && $creditmemo->getVendorOrder()->getId()) {
            $baseOrderRefund = $this->priceCurrency->round(
                $creditmemo->getVendorOrder()->getBaseTotalRefunded() + $creditmemo->getBaseGrandTotal()
            );
            if ($baseOrderRefund > $this->priceCurrency->round($creditmemo->getVendorOrder()->getBaseTotalPaid())) {
                $baseAvailableRefund = $creditmemo->getVendorOrder()->getBaseTotalPaid()
                    - $creditmemo->getVendorOrder()->getBaseTotalRefunded();

                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'The most money available to refund is %1.',
                        $creditmemo->getOrder()->formatBasePrice($baseAvailableRefund)
                    )
                );
            }
        } else {
            $baseOrderRefund = $this->priceCurrency->round(
                $creditmemo->getOrder()->getBaseTotalRefunded() + $creditmemo->getBaseGrandTotal()
            );
            if ($baseOrderRefund > $this->priceCurrency->round($creditmemo->getOrder()->getBaseTotalPaid())) {
                $baseAvailableRefund = $creditmemo->getOrder()->getBaseTotalPaid()
                    - $creditmemo->getOrder()->getBaseTotalRefunded();

                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'The most money available to refund is %1.',
                        $creditmemo->getOrder()->formatBasePrice($baseAvailableRefund)
                    )
                );
            }
        }


        return true;
    }
}
