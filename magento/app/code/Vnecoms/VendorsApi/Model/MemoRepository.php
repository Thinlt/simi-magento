<?php

namespace Vnecoms\VendorsApi\Model;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\Creditmemo\Grid\CollectionFactory as VendorMemoGridCollectionFactory;
use Magento\Framework\Api\DataObjectHelper as DataObjectHelper;
use Vnecoms\VendorsApi\Api\MemoRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Vnecoms\VendorsSales\Model\Service\MemoService;
use Vnecoms\VendorsApi\Api\Data\Sale\MemoInterfaceFactory as MemoDataInterfaceFactory;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

/**
 * Vendor repository.
 */
class MemoRepository implements MemoRepositoryInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var VendorMemoGridCollectionFactory
     */
    protected $vendorMemoGridCollectionFactory;

    /**
     * @var \Vnecoms\VendorsApi\Api\Data\Sale\MemoSearchResultInterfaceFactory
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
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * @var MemoDataInterfaceFactory
     */
    protected $memoDataInterfaceFactory;

    /**
     * MemoRepository constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ApiHelper $helper
     * @param VendorMemoGridCollectionFactory $vendorMemoGridCollectionFactory
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\MemoSearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param MemoDataInterfaceFactory $memoDataInterfaceFactory
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Order\CreditmemoLoader $creditmemoLoader
     * @param CreditmemoSender $creditmemoSender
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ApiHelper $helper,
        VendorMemoGridCollectionFactory $vendorMemoGridCollectionFactory,
        \Vnecoms\VendorsApi\Api\Data\Sale\MemoSearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null,
        \Vnecoms\Vendors\App\Action\Context $context,
        MemoDataInterfaceFactory $memoDataInterfaceFactory,
        \Vnecoms\VendorsSales\Controller\Vendors\Order\CreditmemoLoader $creditmemoLoader,
        CreditmemoSender $creditmemoSender
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->vendorMemoGridCollectionFactory = $vendorMemoGridCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->registry = $context->getCoreRegsitry();
        $this->memoDataInterfaceFactory = $memoDataInterfaceFactory;
        $this->_objectManager = $objectManager;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->creditmemoSender = $creditmemoSender;
    }


    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoSearchResultInterface
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $collection = $this->vendorMemoGridCollectionFactory->create()->addFieldToFilter('vendor_id', $vendorId);

        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $memos = [];
        /** @var \Vnecoms\VendorsSales\Model\ResourceModel\Order\Creditmemo\Grid\Collection $memoModel */
        foreach ($collection as $memoModel) {
            $memoData = $this->memoDataInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $memoData,
                $memoModel->getData(),
                'Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface'
            );
            $memos[] = $memoData;
        }

        $searchResults->setItems($memos);
        return $searchResults;
    }


    /**
     * @param  int $vendorOrderId
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface[] $items
     * @param  string $comment
     * @param  int $doOffline
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createMemo(
        $vendorOrderId,
        $items,
        $comment,
        $doOffline
    ) {
        $data = [];
        $data['comment_text'] = $comment;
        $data['do_offline'] = $doOffline;
        $data['items'] = [];
        foreach ($items as $item){
            $data['items'][$item->getItemId()] = ['qty'=>$item->getQty()];
        }
        $vendorOrder = $this->_objectManager->create('\Vnecoms\VendorsSales\Model\Order')->load($vendorOrderId);

        $this->creditmemoLoader->setVendorOrder($vendorOrder);
        $this->creditmemoLoader->setOrderId($vendorOrder->getOrderId());
        $this->creditmemoLoader->setCreditmemo($data);

        $creditmemo = $this->creditmemoLoader->load();

        if ($creditmemo) {
            if (!$creditmemo->isValidGrandTotal()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The credit memo\'s total must be positive.')
                );
            }

            $creditmemo->setVendorOrder($vendorOrder);

            if (!empty($data['comment_text'])) {
                $creditmemo->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );

                $creditmemo->setCustomerNote($data['comment_text']);
                $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }

            if (isset($data['do_offline'])) {
                //do not allow online refund for Refund to Store Credit
                if (!$data['do_offline'] && !empty($data['refund_customerbalance_return_enable'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Cannot create online refund for Refund to Store Credit.')
                    );
                }
            }
            $creditmemoManagement = $this->_objectManager->create(
                '\Magento\Sales\Api\CreditmemoManagementInterface'
            );
            $creditmemoManagement->refund($creditmemo, (bool)$data['do_offline']);

            if (!empty($data['send_email'])) {
                $this->creditmemoSender->send($creditmemo);
            }
        }

        $memoObject = $this->memoDataInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $memoObject,
            $creditmemo->getData(),
            'Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface'
        );
        return $memoObject;
    }
}

