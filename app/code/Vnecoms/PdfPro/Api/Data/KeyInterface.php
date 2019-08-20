<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Api\Data;

/**
 * Interface KeyInterface.
 */
interface KeyInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getApiKey();

    /**
     * @return string
     */
    public function getStoreIds();

    /**
     * @return string
     */
    public function getCustomerGroupIds();

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @return string
     */
    public function getLogo();

    /**
     * @return string
     */
    public function getComment();

    /**
     * @return string
     */
    public function getOrderTemplate();

    /**
     * @return string
     */
    public function getInvoiceTemplate();

    /**
     * @return string
     */
    public function getShipmentTemplate();

    /**
     * @return string
     */
    public function getCreditmemoTemplate();

    /**
     * @return string
     */
    public function getCss();

    /**
     * @return int
     */
    public function getRtl();

    /**
     * @return string
     */
    public function getWaterImage();

    /**
     * @return string
     */
    public function getWaterType();

    /**
     * @return string
     */
    public function getWaterText();

    /**
     * @return string
     */
    public function getWaterAlpha();

    /**
     * @param $id
     *
     * @return KeyInterface
     */
    public function setId($id);

    /**
     * @param $api_key
     *
     * @return KeyInterface
     */
    public function setApiKey($api_key);

    /**
     * @param $store_ids
     *
     * @return KeyInterface
     */
    public function setStoreIds($store_ids);

    /**
     * @param $customer_group_ids
     *
     * @return KeyInterface
     */
    public function setCustomerGroupIds($customer_group_ids);

    /**
     * @param $priority
     *
     * @return KeyInterface
     */
    public function setPriority($priority);

    /**
     * @param $logo
     *
     * @return KeyInterface
     */
    public function setLogo($logo);

    /**
     * @param $comment
     *
     * @return KeyInterface
     */
    public function setComment($comment);

    /**
     * @param $order_template
     *
     * @return KeyInterface
     */
    public function setOrderTemplate($order_template);

    /**
     * @param $invoice_template
     *
     * @return KeyInterface
     */
    public function setInvoiceTemplate($invoice_template);

    /**
     * @param $shipment_template
     *
     * @return KeyInterface
     */
    public function setShipmentTemplate($shipment_template);

    /**
     * @param $creditmemo_template
     *
     * @return KeyInterface
     */
    public function setCreditmemoTemplate($creditmemo_template);

    /**
     * @param $css
     *
     * @return KeyInterface
     */
    public function setCss($css);

    /**
     * @param $rtl
     *
     * @return KeyInterface
     */
    public function setRtl($rtl);

    /**
     * @param $water_image
     *
     * @return KeyInterface
     */
    public function setWaterImage($water_image);

    /**
     * @param $water_type
     *
     * @return KeyInterface
     */
    public function setWaterType($water_type);

    /**
     * @param $water_image
     *
     * @return KeyInterface
     */
    public function setWaterText($water_image);

    /**
     * @param $water_alpha
     *
     * @return KeyInterface
     */
    public function setWaterAlpha($water_alpha);
}
