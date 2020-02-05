<?php

namespace Vnecoms\VendorsApi\Api\Data\Catalog;

/**
 * @api
 * @since 100.0.2
 */
interface ProductInterface extends \Magento\Catalog\Api\Data\ProductInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const QTY = 'qty';

    /**
     * Product qty
     *
     * @return float|null
     */
    public function getQty();

    /**
     * Set product qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);
    
    /**
     * Get thumbnail url
     *
     * @return string|null
     */
    public function getThumbnailUrl();
    
    /**
     * Set thumbnail url
     *
     * @param string $url
     * @return $this
    */
    public function setThumbnailUrl($url);

}
