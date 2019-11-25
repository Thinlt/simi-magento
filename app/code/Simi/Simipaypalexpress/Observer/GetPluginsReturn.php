<?php

namespace Simi\Simipaypalexpress\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetPluginsReturn implements ObserverInterface
{

    private $simiObjectManager;

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
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();   
        if ($observerObjectData['resource'] == 'ppexpressapis') {
            $observerObjectData['module'] = 'simipaypalexpress';
        }
        $observerObject->setData($observerObjectData);
    }

}
