<?php

namespace Vnecoms\VendorsApi\Model;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\Shipment\Grid\CollectionFactory as VendorShipmentGridCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Vnecoms\VendorsApi\Api\ShipmentRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterfaceFactory as ShipmentDataInterfaceFactory;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;

/**
 * Vendor repository.
 */
class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var VendorShipmentGridCollectionFactory
     */
    protected $vendorShipmentGridCollectionFactory;

    /**
     * @var \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var ShipmentDataInterfaceFactory
     */
    protected $shipmentDataInterfaceFactory;

    /**
     * ShipmentRepository constructor.
     *
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ApiHelper $helper
     * @param VendorShipmentGridCollectionFactory $vendorShipmentGridCollectionFactory
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentSearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param ShipmentDataInterfaceFactory $shipmentDataInterfaceFactory
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param ShipmentSender $shipmentSender
     */
    public function __construct(
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ApiHelper $helper,
        VendorShipmentGridCollectionFactory $vendorShipmentGridCollectionFactory,
        \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentSearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null,
        \Vnecoms\Vendors\App\Action\Context $context,
        ShipmentDataInterfaceFactory $shipmentDataInterfaceFactory,
        \Vnecoms\VendorsSales\Controller\Vendors\Order\ShipmentLoader $shipmentLoader,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        ShipmentSender $shipmentSender
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->vendorShipmentGridCollectionFactory = $vendorShipmentGridCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->registry = $context->getCoreRegsitry();
        $this->shipmentDataInterfaceFactory = $shipmentDataInterfaceFactory;
        $this->shipmentLoader = $shipmentLoader;
        $this->labelGenerator = $labelGenerator;
        $this->shipmentSender = $shipmentSender;
    }

    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $collection = $this->vendorShipmentGridCollectionFactory->create()->addFieldToFilter('vendor_id', $vendorId);


        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $shipments = [];
        /** @var \Vnecoms\VendorsSales\Model\ResourceModel\Order\Shipment\Grid\Collection $shipmentModel */
        foreach ($collection as $shipmentModel) {
            $shipmentData = $this->shipmentDataInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $shipmentData,
                $shipmentModel->getData(),
                'Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface'
            );
            $shipments[] = $shipmentData;
        }

        $searchResults->setItems($shipments);
        return $searchResults;
    }

    /**
     * @param int $customerId
     * @param  int $vendorOrderId
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface[] $items
     * @param  string $comment
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\TrackingInterface[] $trackings
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createShipment(
        $customerId,
        $vendorOrderId,
        $items,
        $comment,
        $trackings
    ) {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $data = [];
        foreach ($items as $item){
            $data['items'][$item['item_id']] = $item['qty'];
        }
        $data['comment_text'] = $comment;

        $dataTracking = [];
        foreach ($trackings as $key => $tracking){
            $dataTracking[$key+1] = ['carrier_code'=>$tracking['carrier_code'],
                'title'=>$tracking['title'],
                'number'=>$tracking['number'],
            ];
        }

        if (!empty($data['comment_text'])) {
            $this->_objectManager->get('Vnecoms\Vendors\Model\Session')->setCommentText($data['comment_text']);
        }

        $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($vendorOrderId);
        if (!$vendorOrder->getId() || $vendorOrder->getVendorId() != $vendor->getId()) {
            throw new LocalizedException(__('The order no longer exists.'));
        }
                
        $this->shipmentLoader->setOrderId($vendorOrder->getOrderId());
        $this->shipmentLoader->setShipment($data);
        $this->shipmentLoader->setTracking($dataTracking);
        $this->shipmentLoader->setVendorOrder($vendorOrder);

        $shipment = $this->shipmentLoader->load();
        if (!$shipment) {
            throw new LocalizedException(__('Can not create shipment'));
        }


        if (!empty($data['comment_text'])) {
            $shipment->addComment(
                $data['comment_text'],
                isset($data['comment_customer_notify']),
                isset($data['is_visible_on_front'])
            );

            $shipment->setCustomerNote($data['comment_text']);
            $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
        }

        $shipment->setVendorOrderId($vendorOrder->getId());

        $shipment->register();

        $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
        $responseAjax = new \Magento\Framework\DataObject();
        $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];

        if ($isNeedCreateLabel) {
            $this->labelGenerator->create($shipment, $this->_request);
            $responseAjax->setOk(true);
        }

        $shipment->setVendorId($vendorOrder->getvendorId());
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->_objectManager->create(
            'Magento\Framework\DB\Transaction'
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        if (!empty($data['send_email'])) {
            $this->shipmentSender->send($shipment);
        }

        $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);

        $shipmentObject = $this->shipmentDataInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $shipmentObject,
            $shipment->getData(),
            'Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface'
        );

        return $shipmentObject;
    }
}

