<?php

namespace Vnecoms\VendorsProfileNotification\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsProfileNotification\Model\ResourceModel\Process\CollectionFactory;
use Vnecoms\VendorsProfileNotification\Model\Process;
use Vnecoms\VendorsProfileNotification\Model\Source\NoticeMessageSetting;

class Predispatch implements ObserverInterface
{
    const MESSAGE_IDENTIFIER    = 'vendor_profile_notification';
    
    const SESSION_KEY           = 'vendors_profile_notification_is_shown';
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $session;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    
    /**
     * @var \Vnecoms\VendorsProfileNotification\Helper\Data
     */
    protected $helper;
    
    /**
     * @param CollectionFactory $collectionFactory
     * @param \Vnecoms\Vendors\Model\Session $session
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Vnecoms\VendorsProfileNotification\Helper\Data $helper
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        \Vnecoms\Vendors\Model\Session $session,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $coreRegistry,
        \Vnecoms\VendorsProfileNotification\Helper\Data $helper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->messageManager = $messageManager;
        $this->coreRegistry = $coreRegistry;
        $this->helper = $helper;
    }
    
    /**
     * Get vendor object
     * 
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor(){
        return $this->session->getVendor();
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helper->isEnabledProfileMessage()) return;
        
        if(
            $this->helper->getProfileMessageSetting() == NoticeMessageSetting::SHOW_ONE_TIME &&
            $this->session->getData(self::SESSION_KEY)
        ) {
            return;
            
        }
        if($this->coreRegistry->registry(self::SESSION_KEY)) return;
        $this->coreRegistry->register(self::SESSION_KEY, true);
        
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', Process::STATUS_ENABLED)
            ->setOrder('sort_order','ASC');;
        
        foreach($collection as $process){
            if(!$process->isCompleted($this->getVendor())){
                $messages = $this->messageManager->getMessages(false)->deleteMessageByIdentifier(self::MESSAGE_IDENTIFIER);
                $this->session->setData(self::SESSION_KEY, true);
                $this->messageManager->addNotice(__("Your Profile is incomplete. Please check all notifications on the top header of your vendor panel."));
                $this->messageManager->getMessages(false)->getLastAddedMessage()->setIdentifier(self::MESSAGE_IDENTIFIER);
                return;
            }
        }
        
        return $this;
    }
}
