<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Api\Data;

/**
 * Interface TemplateInterface.
 */
interface TemplateInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPreview();

    /**
     * @return string
     */
    public function getCssPath();

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
     * @param int $id
     *
     * @return TemplateInterface
     */
    public function setId($id);

    /**
     * @param string $sku
     *
     * @return TemplateInterface
     */
    public function setSku($sku);

    /**
     * @param string $name
     *
     * @return TemplateInterface
     */
    public function setName($name);

    /**
     * @param string $css_path
     *
     * @return TemplateInterface
     */
    public function setCssPath($css_path);

    /**
     * @param string $preview
     *
     * @return TemplateInterface
     */
    public function setPreview($preview);

    /**
     * @param string $order_template
     *
     * @return TemplateInterface
     */
    public function setOrderTemplate($order_template);

    /**
     * @param string $invoice_template
     *
     * @return TemplateInterface
     */
    public function setInvoiceTemplate($invoice_template);

    /**
     * @param string $shipment_template
     *
     * @return TemplateInterface
     */
    public function setShipmentTemplate($shipment_template);

    /**
     * @param string $creditmemo_template
     *
     * @return TemplateInterface
     */
    public function setCreditmemoTemplate($creditmemo_template);
}
