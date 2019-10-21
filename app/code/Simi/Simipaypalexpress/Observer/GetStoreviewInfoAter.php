<?php

namespace Simi\Simipaypalexpress\Observer;

use Magento\Framework\Event\ObserverInterface;

class GetStoreviewInfoAter implements ObserverInterface
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
        $storeviewData = $observerObject->storeviewInfo;  
        if ((int) $this->_getConfig('simipaypalexpress/general/enable_app') != 0) {
            $storeviewData['paypal_express_config'] = array(
                'show_on_product_detail'=>$this->_getConfig('simipaypalexpress/general/product_detail'),
                'show_on_cart'=>$this->_getConfig('simipaypalexpress/general/cart'),
            );
        }
        $observerObject->storeviewInfo = $storeviewData;
    }
    
    protected function _getConfig($config)
    {
        return $this->simiObjectManager
                ->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue($config);
    }

}
