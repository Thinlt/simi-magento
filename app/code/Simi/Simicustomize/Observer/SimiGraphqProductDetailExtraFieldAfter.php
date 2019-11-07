<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;


class SimiGraphqProductDetailExtraFieldAfter implements ObserverInterface {
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $extraFieldObject = $observer->getEvent()->getData('object');
        $data = $observer->getEvent()->getData('data');
        if (isset($data['attribute_values']['type_id'])) {
            if ($data['attribute_values']['type_id'] == 'configurable') {
                $product = null;
                $registry = $this->simiObjectManager->get('\Magento\Framework\Registry');
                if ($registry->registry('product')) {
                    $product = $registry->registry('current_product');
                }
                if ($product && $product->getId()) {
                    $options = $this->simiObjectManager->get('Simi\Simicustomize\Helper\Options\Configurable')->getOptions($product);
                    if ($extraFieldObject && $extraFieldObject->extraFields && isset($extraFieldObject->extraFields['app_options'])) {
                        $extraFieldObject->extraFields['app_options'] = $options;
                    }
                }
            }
        }
    }
}