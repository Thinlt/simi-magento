<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsSales\Model\ResourceModel\Order\Creditmemo\Relation;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

/**
 * Class Relation
 */
class Refund implements RelationInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->priceCurrency = $priceCurrency;
        $this->_objectManager =$objectManagerInterface;
    }

    /**
     * Process relations for CreditMemo
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @throws \Exception
     * @return void
     */
    public function processRelation(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $object */
        if ($object->getState() == \Magento\Sales\Model\Order\Creditmemo::STATE_REFUNDED) {
            $this->prepareOrder($object);
            if ($object->getInvoice()) {
                $this->prepareInvoice($object);
                $this->invoiceRepository->save($object->getInvoice());
            }
        }
    }

    /**
     * Prepare order data for refund
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    protected function prepareOrder(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $vendorOrderId  = $creditmemo->getVendorOrderId();
        $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($vendorOrderId);
        if (!$vendorOrder->getId()) {
            return;
        }
        $baseOrderRefund = $this->priceCurrency->round(
            $vendorOrder->getBaseTotalRefunded() + $creditmemo->getBaseGrandTotal()
        );
        $orderRefund = $this->priceCurrency->round(
            $vendorOrder->getTotalRefunded() + $creditmemo->getGrandTotal()
        );
        $vendorOrder->setBaseTotalRefunded($baseOrderRefund);
        $vendorOrder->setTotalRefunded($orderRefund);

        $vendorOrder->setBaseSubtotalRefunded($vendorOrder->getBaseSubtotalRefunded() + $creditmemo->getBaseSubtotal());
        $vendorOrder->setSubtotalRefunded($vendorOrder->getSubtotalRefunded() + $creditmemo->getSubtotal());

        $vendorOrder->setBaseTaxRefunded($vendorOrder->getBaseTaxRefunded() + $creditmemo->getBaseTaxAmount());
        $vendorOrder->setTaxRefunded($vendorOrder->getTaxRefunded() + $creditmemo->getTaxAmount());
        $vendorOrder->setBaseDiscountTaxCompensationRefunded(
            $vendorOrder->getBaseDiscountTaxCompensationRefunded() + $creditmemo->getBaseDiscountTaxCompensationAmount()
        );
        $vendorOrder->setDiscountTaxCompensationRefunded(
            $vendorOrder->getDiscountTaxCompensationRefunded() + $creditmemo->getDiscountTaxCompensationAmount()
        );

        $vendorOrder->setBaseShippingRefunded($vendorOrder->getBaseShippingRefunded() + $creditmemo->getBaseShippingAmount());
        $vendorOrder->setShippingRefunded($vendorOrder->getShippingRefunded() + $creditmemo->getShippingAmount());

        $vendorOrder->setBaseShippingTaxRefunded(
            $vendorOrder->getBaseShippingTaxRefunded() + $creditmemo->getBaseShippingTaxAmount()
        );
        $vendorOrder->setShippingTaxRefunded($vendorOrder->getShippingTaxRefunded() + $creditmemo->getShippingTaxAmount());

        $vendorOrder->setAdjustmentPositive($vendorOrder->getAdjustmentPositive() + $creditmemo->getAdjustmentPositive());
        $vendorOrder->setBaseAdjustmentPositive(
            $vendorOrder->getBaseAdjustmentPositive() + $creditmemo->getBaseAdjustmentPositive()
        );

        $vendorOrder->setAdjustmentNegative($vendorOrder->getAdjustmentNegative() + $creditmemo->getAdjustmentNegative());
        $vendorOrder->setBaseAdjustmentNegative(
            $vendorOrder->getBaseAdjustmentNegative() + $creditmemo->getBaseAdjustmentNegative()
        );

        $vendorOrder->setDiscountRefunded($vendorOrder->getDiscountRefunded() + $creditmemo->getDiscountAmount());
        $vendorOrder->setBaseDiscountRefunded($vendorOrder->getBaseDiscountRefunded() + $creditmemo->getBaseDiscountAmount());

        if ($creditmemo->getDoTransaction()) {
            $vendorOrder->setTotalOnlineRefunded($vendorOrder->getTotalOnlineRefunded() + $creditmemo->getGrandTotal());
            $vendorOrder->setBaseTotalOnlineRefunded($vendorOrder->getBaseTotalOnlineRefunded() + $creditmemo->getBaseGrandTotal());
        } else {
            $vendorOrder->setTotalOfflineRefunded($vendorOrder->getTotalOfflineRefunded() + $creditmemo->getGrandTotal());
            $vendorOrder->setBaseTotalOfflineRefunded(
                $vendorOrder->getBaseTotalOfflineRefunded() + $creditmemo->getBaseGrandTotal()
            );
        }

        $vendorOrder->setBaseTotalInvoicedCost(
            $vendorOrder->getBaseTotalInvoicedCost() - $creditmemo->getBaseCost()
        );

        $vendorOrder->save();
    }

    /**
     * Prepare invoice data for refund
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    protected function prepareInvoice(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        if ($creditmemo->getInvoice()) {
            $creditmemo->getInvoice()->setIsUsedForRefund(true);
            $creditmemo->getInvoice()->setBaseTotalRefunded(
                $creditmemo->getInvoice()->getBaseTotalRefunded() + $creditmemo->getBaseGrandTotal()
            );
            $creditmemo->setInvoiceId($creditmemo->getInvoice()->getId());
        }
    }
}
