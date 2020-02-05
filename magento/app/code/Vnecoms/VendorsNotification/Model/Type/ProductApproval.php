<?php
namespace Vnecoms\VendorsNotification\Model\Type;

class ProductApproval extends DefaultType
{

    /**
     * @var Type code
     */
    const CODE = 'product_approval';
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsNotification\Model\Type\TypeInterface::getIconClass()
     */
    public function getIconClass()
    {
        return 'fa fa-cube text-red';
    }
    
    /**
     * Get order URL
     * @see \Vnecoms\VendorsNotification\Model\Type\TypeInterface::getUrl()
     */
    public function getUrl()
    {
        try {
            $additionalInfo = unserialize($this->_notification->getAdditionalInfo());
            $productId = isset($additionalInfo['id'])?$additionalInfo['id']:0;
            return $productId?$this->_urlBuilder->getUrl('catalog/product/edit', ['id' => $productId]):$this->_urlBuilder->getUrl('catalog/product');
        } catch (\Exception $e) {
            return parent::getUrl();
        }
    }
}
