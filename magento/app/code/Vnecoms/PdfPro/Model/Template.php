<?php

namespace Vnecoms\PdfPro\Model;

use Magento\Framework\Model\AbstractModel;
use Vnecoms\PdfPro\Api\Data\TemplateInterface;

/**
 * Class Template.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Template extends AbstractModel implements TemplateInterface
{
    const ID = 'id';
    const NAME = 'name';
    const SKU = 'sku';
    const ORDER_TEMPLATE = 'order_template';
    const INVOICE_TEMPLATE = 'invoice_template';
    const SHIPMENT_TEMPLATE = 'shipment_template';
    const CREDITMEMO_TEMPLATE = 'creditmemo_template';
    const PREVIEW = 'preview_image';
    const CSS = 'css_path';
    const BASE_MEDIA_PATH = 'ves_pdfpro/tmp';

    protected function _construct()
    {
        $this->_init('Vnecoms\PdfPro\Model\ResourceModel\Template');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function getCssPath()
    {
        return $this->getData(self::CSS);
    }

    public function getPreview()
    {
        return $this->getData(self::PREVIEW);
    }

    public function getOrderTemplate()
    {
        return $this->getData(self::ORDER_TEMPLATE);
    }

    public function getInvoiceTemplate()
    {
        return $this->getData(self::INVOICE_TEMPLATE);
    }

    public function getShipmentTemplate()
    {
        return $this->getData(self::SHIPMENT_TEMPLATE);
    }

    public function getCreditmemoTemplate()
    {
        return $this->getData(self::CREDITMEMO_TEMPLATE);
    }

    public function setId($id)
    {
        $this->setData(self::ID, $id);

        return $this;
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);

        return $this;
    }

    public function setSku($sku)
    {
        $this->setData(self::SKU, $sku);

        return $this;
    }

    public function setCssPath($css_path)
    {
        $this->setData(self::CSS, $css_path);

        return $this;
    }

    public function setPreview($preview_image)
    {
        $this->setData(self::PREVIEW, $preview_image);

        return $this;
    }

    public function setOrderTemplate($order_template)
    {
        $this->setData(self::ORDER_TEMPLATE, $order_template);

        return $this;
    }

    public function setInvoiceTemplate($invoice_template)
    {
        $this->setData(self::INVOICE_TEMPLATE, $invoice_template);

        return $this;
    }

    public function setShipmentTemplate($shipment_template)
    {
        $this->setData(self::SHIPMENT_TEMPLATE, $shipment_template);

        return $this;
    }

    public function setCreditmemoTemplate($creditmemo_template)
    {
        $this->setData(self::CREDITMEMO_TEMPLATE, $creditmemo_template);

        return $this;
    }
}
