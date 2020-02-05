<?php

namespace Simi\Simipaypalexpress\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderPlacedAfter implements ObserverInterface
{

    private $simiObjectManager;
    public $new_added_product_sku = '';

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
   
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $urlInterface = $this->simiObjectManager->get('Magento\Framework\UrlInterface');
        $currentUrl = $urlInterface->getCurrentUrl();
        $pos = strpos($currentUrl, 'ppexpressapis/place');
        if ($pos === false) {
           return;
        }
        $orderId = $observer->getEvent()->getOrder()->getId();
        $newTransaction = $this->simiObjectManager->create('Simi\Simiconnector\Model\Appreport');
        $newTransaction->setOrderId($orderId);
        try {
            $newTransaction->save();
        } catch (Exception $exc) {
            
        }
    }

}
