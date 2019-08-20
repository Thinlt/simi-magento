<?php

namespace Vnecoms\VendorsCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class CalculateCommission implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $_vendorFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    protected $itemsArray;
    
     /**
      * @param \Magento\Framework\Event\ManagerInterface $eventManager
      * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
      * @param \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
      * @param \Magento\Catalog\Model\ProductFactory $productFactory
      */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_vendorFactory = $vendorFactory;
        $this->_productFactory = $productFactory;
    }
    

    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getInvoice();
        $vendors = [];
        /*Calculate commission and create transaction for each item.*/
        $allItems = $invoice->getAllItems();
        foreach ($allItems as $item) {
            if(
                $item->getCommission() !== null &&
                $item->getCommission() !== ''
            ) continue;
            
            $orderItem  = $item->getOrderItem();
            if ($orderItem->getParentItemId()) {
                continue;
            }
            $product = $this->_productFactory->create()->load($orderItem->getProductId());
            $vendorId = $product->getVendorId();
            if(!$vendorId) continue;
            
            if(!isset($vendors[$vendorId])){
                $vendors[$vendorId] = $this->_vendorFactory->create()->load($vendorId);
            }
            $vendor = $vendors[$vendorId];
            if(!$vendor->getId()) continue;
            
            $amount = $item->getBaseRowTotal();
            $fee = 0;
            $commissionObj  = new \Magento\Framework\DataObject(['fee' => $fee]);
            
            if (
                $orderItem->getHasChildren()
                && $orderItem->isChildrenCalculated()
            ) {
                $childItems = $this->getChildren($item, $allItems);
                if ($childItems) {
                    foreach ($childItems as $child) {
                        $childProduct = $this->_productFactory->create()->load($child->getOrderItem()->getProductId());
                        $this->_eventManager->dispatch(
                            'ves_vendorscredit_calculate_commission',
                            [
                                'commission'    => $commissionObj,
                                'invoice_item'  => $child,
                                'product'       => $childProduct,
                                'vendor'        => $vendor,
                            ]
                        );
                    }
                }
            } else {
                if($product->getTypeId() == Configurable::TYPE_CODE){
                    $product->setPrice($item->getPrice())->setPriceCalculation(false);
                }
                $this->_eventManager->dispatch(
                    'ves_vendorscredit_calculate_commission',
                    [
                        'commission'    => $commissionObj,
                        'invoice_item'  => $item,
                        'product'       => $product,
                        'vendor'        => $vendor,
                    ]
                );
            }
            $fee = $commissionObj->getFee();

            /*Do nothing if the fee is zero*/
            if ($fee <= 0) {
                continue;
            }
            
            $additionalDescription = $commissionObj->getDescriptions();
            if ($additionalDescription && is_array($additionalDescription)) {
                $tmpDescription = '<ul style="list-style: inside;">';
                foreach ($additionalDescription as $description) {
                    $tmpDescription .='<li>'.$description.'</li>';
                }
                $tmpDescription .="</ul>";
            
                $additionalDescription = $tmpDescription;
            } else {
                $additionalDescription = '';
            }
            $item->setCommission($fee);
            $item->setCommissionDescription($additionalDescription);
        }
        return $this;
    }
    
    /**
    * Getting all available children
    *
    * @param $item
    * @param $allItems
    * @return array|null
    */
    public function getChildren($item, $allItems)
    {
        if(!$this->itemsArray){
            $this->itemsArray = [];
            foreach ($allItems as $invoiceItem) {
                $parentItem = $invoiceItem->getOrderItem()->getParentItem();
                if (!$parentItem) continue;
                $this->itemsArray[$parentItem->getId()][$invoiceItem->getOrderItemId()] = $invoiceItem;
            }
        }
        return isset($this->itemsArray[$item->getOrderItem()->getId()]) ? $this->itemsArray[$item->getOrderItem()->getId()] : null;
    }
    
}
