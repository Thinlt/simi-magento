<?php

namespace Simi\Simistorelocator\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Description of ChangeApiResource
 *
 * @author scott
 */
class SimiSimiconnectorModelServerInitialize implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'storelocations') {
            $observerObjectData['module'] = 'simistorelocator';
        }elseif ($observerObjectData['resource'] == 'storelocatortags'){
            $observerObjectData['module'] = 'simistorelocator';
        }
        $observerObject->setData($observerObjectData);
    }
}
