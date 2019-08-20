<?php

namespace Vnecoms\PdfPro\Model;

use Magento\Framework\Model\AbstractModel;
use Vnecoms\PdfPro\Api\Data\KeyInterface;

/**
 * Class Key.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Key extends AbstractModel implements KeyInterface
{
    const ID = 'entity_id';
    const API_KEY = 'api_key';
    const LOGO = 'logo';
    const STORE_IDS = 'store_ids';
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    const PRIORITY = 'priority';
    const COMMENT = 'comment';
    const ORDER_TEMPLATE = 'order_template';
    const INVOICE_TEMPLATE = 'invoice_template';
    const SHIPMENT_TEMPLATE = 'shipment_template';
    const CREDITMEMO_TEMPLATE = 'creditmemo_template';
    const CSS = 'css';
    const RTL = 'rtl';
    const WATER_IMAGE = 'water_image';
    const WATER_TYPE = 'water_type';
    const WATER_ALPHA = 'water_alpha';
    const WATER_TEXT = 'water_text';
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    protected $_eventPrefix = 'pdfpro_key';

    protected function _construct()
    {
        $this->_init('Vnecoms\PdfPro\Model\ResourceModel\Key');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getData(self::API_KEY);
    }

    /**
     * @return string
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * @return string
     */
    public function getCustomerGroupIds()
    {
        return $this->getData(self::CUSTOMER_GROUP_IDS);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->getData(self::LOGO);
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * @return string
     */
    public function getOrderTemplate()
    {
        return $this->getData(self::ORDER_TEMPLATE);
    }

    /**
     * @return string
     */
    public function getInvoiceTemplate()
    {
        return $this->getData(self::INVOICE_TEMPLATE);
    }

    /**
     * @return string
     */
    public function getShipmentTemplate()
    {
        return $this->getData(self::SHIPMENT_TEMPLATE);
    }

    /**
     * @return string
     */
    public function getCreditmemoTemplate()
    {
        return $this->getData(self::CREDITMEMO_TEMPLATE);
    }

    /**
     * @return string
     */
    public function getCss()
    {
        return $this->getData(self::CSS);
    }

    /**
     * @return int
     */
    public function getRtl()
    {
        return $this->getData(self::RTL);
    }

    /**
     * @return string
     */
    public function getWaterImage()
    {
        return $this->getData(self::WATER_IMAGE);
    }

    /**
     * @return string
     */
    public function getWaterType()
    {
        return $this->getData(self::WATER_TYPE);
    }

    /**
     * @return string
     */
    public function getWaterText()
    {
        return $this->getData(self::WATER_TEXT);
    }

    /**
     * @return string
     */
    public function getWaterAlpha()
    {
        return $this->getData(self::WATER_ALPHA);
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->setData(self::ID, $id);

        return $this;
    }

    /**
     * @param $api_key
     *
     * @return $this
     */
    public function setApiKey($api_key)
    {
        $this->setData(self::API_KEY, $api_key);

        return $this;
    }

    /**
     * @param $store_ids
     *
     * @return $this
     */
    public function setStoreIds($store_ids)
    {
        $this->setData(self::STORE_IDS, $store_ids);

        return $this;
    }

    /**
     * @param $customer_group_ids
     *
     * @return $this
     */
    public function setCustomerGroupIds($customer_group_ids)
    {
        $this->setData(self::CUSTOMER_GROUP_IDS, $customer_group_ids);

        return $this;
    }

    /**
     * @param $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->setData(self::PRIORITY, $priority);

        return $this;
    }

    /**
     * @param $logo
     *
     * @return $this
     */
    public function setLogo($logo)
    {
        $this->setData(self::LOGO, $logo);

        return $this;
    }

    /**
     * @param $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->setData(self::COMMENT, $comment);

        return $this;
    }

    /**
     * @param $order_template
     *
     * @return $this
     */
    public function setOrderTemplate($order_template)
    {
        $this->setData(self::ORDER_TEMPLATE, $order_template);

        return $this;
    }

    /**
     * @param $invoice_template
     *
     * @return $this
     */
    public function setInvoiceTemplate($invoice_template)
    {
        $this->setData(self::INVOICE_TEMPLATE, $invoice_template);

        return $this;
    }

    /**
     * @param $shipment_template
     *
     * @return $this
     */
    public function setShipmentTemplate($shipment_template)
    {
        $this->setData(self::SHIPMENT_TEMPLATE, $shipment_template);

        return $this;
    }

    /**
     * @param $creditmemo_template
     *
     * @return $this
     */
    public function setCreditmemoTemplate($creditmemo_template)
    {
        $this->setData(self::CREDITMEMO_TEMPLATE, $creditmemo_template);

        return $this;
    }

    /**
     * @param $css
     *
     * @return $this
     */
    public function setCss($css)
    {
        $this->setData(self::CSS, $css);

        return $this;
    }

    /**
     * @param $rtl
     *
     * @return $this
     */
    public function setRtl($rtl)
    {
        $this->setData(self::RTL, $rtl);

        return $this;
    }

    /**
     * @param $water_image
     *
     * @return $this
     */
    public function setWaterImage($water_image)
    {
        $this->setData(self::WATER_IMAGE, $water_image);

        return $this;
    }

    /**
     * @param $water_type
     *
     * @return $this
     */
    public function setWaterType($water_type)
    {
        $this->setData(self::WATER_TYPE, $water_type);

        return $this;
    }

    /**
     * @param $water_image
     *
     * @return $this
     */
    public function setWaterText($water_image)
    {
        $this->setData(self::WATER_TEXT, $water_image);

        return $this;
    }

    /**
     * @param $water_alpha
     *
     * @return $this
     */
    public function setWaterAlpha($water_alpha)
    {
        $this->setData(self::WATER_ALPHA, $water_alpha);

        return $this;
    }
}
