<?php
namespace Vnecoms\VendorsNotification\Model\Type;

class Sales extends DefaultType
{

    /**
     * @var Type code
     */
    const CODE = 'sales';
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsNotification\Model\Type\TypeInterface::getIconClass()
     */
    public function getIconClass()
    {
        return 'fa fa-shopping-cart text-green';
    }
    
    /**
     * Get order URL
     * @see \Vnecoms\VendorsNotification\Model\Type\TypeInterface::getUrl()
     */
    public function getUrl()
    {
        try {
            $additionalInfo = unserialize($this->_notification->getAdditionalInfo());
            $orderId = isset($additionalInfo['id'])?$additionalInfo['id']:0;
            return $orderId?$this->_urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]):$this->_urlBuilder->getUrl('sales/order');
        } catch (\Exception $e) {
            return parent::getUrl();
        }
    }
}
