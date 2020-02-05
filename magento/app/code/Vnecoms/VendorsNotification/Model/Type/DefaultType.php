<?php
namespace Vnecoms\VendorsNotification\Model\Type;

use Vnecoms\VendorsNotification\Model\Type\TypeInterface;
use Vnecoms\VendorsNotification\Model\Notification;
use Vnecoms\Vendors\Model\UrlInterface;

class DefaultType implements TypeInterface
{

    /**
     * @var \Vnecoms\VendorsNotification\Model\Notification
     */
    protected $_notification;
    
    /**
     * @var \Vnecoms\Vendors\Model\UrlInterface
     */
    protected $_urlBuilder;
    
    /**
     * Constructor
     *
     * @param Notification $notification
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Notification $notification,
        UrlInterface $urlBuilder
    ) {
        $this->_notification = $notification;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsNotification\Model\Type\TypeInterface::getMessage()
     */
    public function getMessage()
    {
        return $this->_notification->getMessage();
    }
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsNotification\Model\Type\TypeInterface::getIconClass()
     */
    public function getIconClass()
    {
        return 'fa fa-envelope text-red';
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsNotification\Model\Type\TypeInterface::getUrl()
     */
    public function getUrl()
    {
        return '#';
    }
}
