<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Api;

/**
 * Interface OrderInterface.
 */
interface PrintInterface
{
    /**
     * @param string $orderId
     * @param string $customerId
     * @return string
     */
    public function printOrder($orderId, $customerId);

    /**
     * @param string $invoiceId
     * @param string $customerId
     * @return string
     */
    public function printInvoice($invoiceId, $customerId);

    /**
     * @param string $shipmentId
     * @param string $customerId
     * @return string
     */
    public function printShipment($shipmentId, $customerId);

    /**
     * @param string $creditmemoId
     * @param string $customerId
     * @return string
     */
    public function printCreditmemo($creditmemoId, $customerId);

}
