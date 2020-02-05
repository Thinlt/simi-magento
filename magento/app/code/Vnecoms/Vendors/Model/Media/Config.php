<?php
namespace Vnecoms\Vendors\Model\Media;

class Config extends \Magento\Catalog\Model\Product\Media\Config
{
    /**
     * Filesystem directory path of product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaPathAddition()
    {
        return 'ves_vendors/logo';
    }

    /**
     * Web-based directory path of product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaUrlAddition()
    {
        return 'ves_vendors/logo';
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return 'ves_vendors/logo';
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'ves_vendors/logo';
    }
}
