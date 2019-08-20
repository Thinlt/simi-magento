<?php
namespace Vnecoms\VendorsNotification\Model;

class Notification extends \Magento\Framework\Model\AbstractModel
{

    const ENTITY = 'vendor_notification';
    const DEFAULT_NOTIFICATION_TYPE = 'Vnecoms\VendorsNotification\Model\Type\DefaultType';
    
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'vendor_notification';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'notification';

    /**
     * @var All notification types classes
     */
    protected $_notificationTypes;
    
    /**
     * @var \Vnecoms\VendorsNotification\Model\Type\TypeInterface
     */
    protected $_notificationType;
    
    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Vnecoms\VendorsNotification\Model\ResourceModel\Notification');
    }
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $notificationTypes
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $notificationTypes = [],
        array $data = []
    ) {
        $this->_notificationTypes = $notificationTypes;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
     * Get Notification Type
     *
     * @return \Vnecoms\VendorsNotification\Model\Type\TypeInterface
     */
    public function getNotificationType()
    {
        if (!$this->_notificationType) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            if (isset($this->_notificationTypes[$this->getType()])) {
                $this->_notificationType = $om->create($this->_notificationTypes[$this->getType()], ['notification' => $this]);
            } else {
                $this->_notificationType = $om->create(self::DEFAULT_NOTIFICATION_TYPE, ['notification' => $this]);
            }
        }
        
        return $this->_notificationType;
    }
}
