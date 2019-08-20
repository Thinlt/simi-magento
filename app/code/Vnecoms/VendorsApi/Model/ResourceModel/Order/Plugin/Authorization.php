<?php

namespace Vnecoms\VendorsApi\Model\ResourceModel\Order\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order as ResourceOrder;

class Authorization
{
    const VENDOR_ORDER_IDS_KEY = 'vendor_api_auth_order_ids';
    
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var \Vnecoms\VendorsApi\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    
    /**
     * @param UserContextInterface $userContext
     * @param \Vnecoms\VendorsApi\Helper\Data $helper
     * @param \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        UserContextInterface $userContext,
        \Vnecoms\VendorsApi\Helper\Data $helper,
        \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->userContext              = $userContext;
        $this->helper                   = $helper;
        $this->orderCollectionFactory   = $orderCollectionFactory;
        $this->coreRegistry             = $coreRegistry;
    }

    /**
     * @param ResourceOrder $subject
     * @param ResourceOrder $result
     * @param \Magento\Framework\Model\AbstractModel $order
     * @return ResourceOrder
     */
    public function afterLoad(
        ResourceOrder $subject,
        ResourceOrder $result,
        \Magento\Framework\Model\AbstractModel $order
    ) {
        if ($order instanceof Order) {
            if (!$this->isAllowed($order)) {
                throw NoSuchEntityException::singleField('orderId', $order->getId());
            }
        }
        return $result;
    }

    /**
     * Checks if order is allowed for current customer
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function isAllowed(Order $order)
    {
        if($this->userContext->getUserType() != UserContextInterface::USER_TYPE_CUSTOMER) return true;
        
        $vendor = $this->helper->getVendorByCustomerId($this->userContext->getUserId());
        if(!$vendor->getId()) return $order->getCustomerId() == $this->userContext->getUserId();
        
        if(!$this->coreRegistry->registry(self::VENDOR_ORDER_IDS_KEY)){
            $collection = $this->orderCollectionFactory->create()
                ->addFieldToFilter('vendor_id', $vendor->getId());
            $this->coreRegistry->register(self::VENDOR_ORDER_IDS_KEY, $collection->getColumnValues('order_id'));
        }
        
        $vendorOrderIds = $this->coreRegistry->registry(self::VENDOR_ORDER_IDS_KEY);
        
        return in_array($order->getId(), $vendorOrderIds)?true:$order->getCustomerId() == $this->userContext->getUserId();
    }
}
