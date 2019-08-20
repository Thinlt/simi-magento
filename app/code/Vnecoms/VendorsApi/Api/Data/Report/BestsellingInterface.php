<?php

namespace Vnecoms\VendorsApi\Api\Data\Report;

interface BestsellingInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID                = 'product_id';
    const NAME              = 'name';
    const PRICE             = 'price';
    const QTY               = 'qty';
    
    /**#@-*/
    
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();
    
    /**
     * Set vendor id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);
    
    /**
     * Get product name
     * 
     * @return string
     */
    public function getName();
    
    /**
     * Set product name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);
    
    /**
     * Get price
     *
     * @return string
     */
    public function getPrice();
    
    /**
     * Set price
     *
     * @param string $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get ordered qty
     *
     * @return float
     */
    public function getQty();
    
    /**
     * Set ordered qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

}
