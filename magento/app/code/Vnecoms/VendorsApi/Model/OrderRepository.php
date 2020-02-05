<?php

namespace Vnecoms\VendorsApi\Model;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory as VendorOrderCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Vendor repository.
 */
class OrderRepository implements \Vnecoms\VendorsApi\Api\OrderRepositoryInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;
    
    /**
     * @var VendorOrderCollectionFactory
     */
    protected $vendorOrderCollectionFactory;


    /**
     * @var \Vnecoms\VendorsApi\Api\Data\Sale\OrderSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Vnecoms\VendorsApi\Api\Data\OrderInterfaceFactory
     */
    protected $dataOrderFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;
	
	/**
	 * @var \Vnecoms\VendorsSales\Model\OrderFactory
	 */
	protected $vendorOrderFactory;
	
    /**
     * @param ApiHelper $helper
     * @param VendorOrderCollectionFactory $vendorOrderCollectionFactory
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\OrderSearchResultInterfaceFactory $searchResultFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterfaceFactory $dataOrderFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Vnecoms\VendorsSales\Model\OrderFactory $vendorOrderFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ApiHelper $helper,
        VendorOrderCollectionFactory $vendorOrderCollectionFactory,
        \Vnecoms\VendorsApi\Api\Data\Sale\OrderSearchResultInterfaceFactory $searchResultFactory,
        DataObjectHelper $dataObjectHelper,
        \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterfaceFactory $dataOrderFactory,
        DataObjectProcessor $dataObjectProcessor,
		\Magento\Catalog\Model\ProductFactory $productFactory,
        \Vnecoms\VendorsSales\Model\OrderFactory $vendorOrderFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->helper = $helper;
        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataOrderFactory = $dataOrderFactory;
        $this->dataObjectProcessor    = $dataObjectProcessor;
		$this->productFactory         = $productFactory;
		$this->vendorOrderFactory     = $vendorOrderFactory;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * @param int $customerId
     * @param int $orderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface
     */
    public function getOrder($customerId, $orderId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $collection = $this->vendorOrderCollectionFactory->create()
            ->addFieldToFilter('main_table.entity_id', $orderId);
        $collection->join(
            ['order_grid' => $collection->getTable('sales_order_grid')],
            'main_table.order_id=order_grid.entity_id',
            [
                'increment_id',
                'store_id',
                'store_name',
                'base_currency_code',
                'order_currency_code',
                'shipping_name',
                'billing_name',
                'shipping_and_handling',
                'total_refunded',
                'customer_name',
                'customer_email',
                'customer_group',
                'payment_method',
            ]
        );
        
        if(!$collection->count()) throw LocalizedException(__('The order does not exist'));
        $vendorOrder = $collection->getFirstItem();

        if($vendorOrder->getVendorId() != $vendor->getId()) throw LocalizedException(__('The order does not exist'));
        
        $result = $this->dataOrderFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $result,
            $vendorOrder->getData(),
            'Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface'
        );
        $this->addAdditionalInfoToOrderResult($result, $vendorOrder);
        return $result;
    }
    
    /**
     * @param $searchCriteria
     * @param int $customerId
     * @see \Vnecoms\VendorsApi\Api\OrderRepositoryInterface::getList()
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $collection = $this->vendorOrderCollectionFactory->create()->addFieldToFilter('vendor_id', $vendorId);
        $collection->join(
            ['order_grid' => $collection->getTable('sales_order_grid')],
            'main_table.order_id=order_grid.entity_id',
            [
                'increment_id',
                'store_id',
                'store_name',
                'base_currency_code',
                'order_currency_code',
                'shipping_name',
                'billing_name',
                'shipping_and_handling',
                'total_refunded',
                'customer_name',
                'customer_email',
                'customer_group',
                'payment_method',
            ]
        );
        $this->getCollectionProcessor();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $orders = [];
        /** @var Order $orderModel */
        foreach ($collection as $orderModel) {
            $orderData = $this->dataOrderFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $orderData,
                $orderModel->getData(),
                'Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface'
            );
            $this->addAdditionalInfoToOrderResult($orderData, $orderModel);
            
            $orders[] = $orderData;
        }
        $searchResults->setItems($orders);
        return $searchResults;
    }

    /**
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface $orderResult
     * @param \Vnecoms\VendorsSales\Model\Order $vendorOrder
     */
    protected function addAdditionalInfoToOrderResult(
        \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface $orderResult,
        \Vnecoms\VendorsSales\Model\Order $vendorOrder
    ){
        $order = $vendorOrder->getOrder();
        $orderResult->setPayment($order->getPayment());
        $orderResult->setBillingAddress($order->getBillingAddress());
        $orderResult->setShippingAddress($order->getShippingAddress());
        $items = $vendorOrder->getAllItems();
        foreach($items as $item){
            $product = $this->productFactory->create()->load($item->getProductId());
            if(!$product->getId()) continue;
            $item->setThumbnail($product->getThumbnail());
        
            $options = $this->getItemOptions($item);
            if(sizeof($options)){
                $item->setItemOptions($options);
            }
        }
        $orderResult->setItems($items);
        $orderResult->setCanCancel($vendorOrder->canCancel());
        $orderResult->setCanShip($vendorOrder->canShip());
        $orderResult->setCanInvoice($vendorOrder->canInvoice());
        $orderResult->setCanCreditMemo($vendorOrder->canCreditMemo());
    }
    
    /**
     * Get Options of items.
     *
     * @param  $item
     *
     * @return array:
     */
    protected function getItemOptions($item)
    {
        $result = [];
        if ($options = $item->getProductOptions()) {
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
        }
        $optionArr = [];
        foreach ($result as $option) {
            $otp = \Magento\Framework\App\ObjectManager::getInstance()->create(
                'Vnecoms\VendorsApi\Model\Data\Sale\Order\ItemOption'
            );
            $otp->setLabel(strip_tags($option['label']));
    
            if ($option['value']) {
                $printValue = isset($option['print_value'])
                ? $option['print_value'] : strip_tags($option['value']);
                
                $otp->setValue($printValue);
            }
            
            $optionArr[] = $otp;
        }
        return $optionArr;
    }
    
    /**
     * Retrieve collection processor
     *
     * @deprecated 101.1.0
     * @return VendorsSalesApiCollectionProcessor
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Vnecoms\VendorsApi\Model\Api\SearchCriteria\VendorsOrderApiCollectionProcessor'
            );
        }
        return $this->collectionProcessor;
    }
    
    /**
     * @param int $customerId
     * @param int $orderId
     * @return bool
     */
    public function cancel($customerId, $orderId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorOrder = $this->vendorOrderFactory->create()->load($orderId);
        
        if($vendorOrder->getVendorId() != $vendor->getId()){
            throw LocalizedException(__('The order does not exist'));
        }
        if (!$vendorOrder->canCancel()) return false;
        
        $vendorOrder->cancel();
        return true;
    }
}

