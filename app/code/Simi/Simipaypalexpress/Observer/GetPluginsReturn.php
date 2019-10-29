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
        $object = $observer->getEvent()->getData('object');
        $data = $observer->getEvent()->getData('data');
        if ($data['resource'] == 'ppexpressapis') {
            $data['module'] = 'simipaypalexpress';
        }
        $object->setData($data);
    }

}
