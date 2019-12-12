<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simicustompayment\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodIsActive implements ObserverInterface {

    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
   
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $method = $observer['method_instance'];
        if ($method->getCode() == 'simicustompayment') {
            if (!strpos($this->simiObjectManager->get('\Magento\Framework\Url')->getCurrentUrl(), 'simiconnector')) {
                $result = $observer->getEvent()->getResult();
                $result->setData('is_available', false);
            }
        }
    }

}
