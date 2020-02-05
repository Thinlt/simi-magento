<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\GiftcardPools\Code;

use Aheadworks\Giftcard\Api\PoolCodeRepositoryInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Pool\Code\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Vnecoms\Vendors\App\Action\Context;

/**
 * Class MassDelete
 *
 * @package \Vnecoms\Vendors\Controller\Vendors\Action\MassDelete
 */
class MassDelete extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::giftcard_pools';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var PoolCodeRepositoryInterface
     */
    private $poolCodeRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param PoolCodeRepositoryInterface $poolCodeRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PoolCodeRepositoryInterface $poolCodeRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->poolCodeRepository = $poolCodeRepository;
    }

    /**
     * Delete mass action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection->getItems() as $item) {
                $this->poolCodeRepository->deleteById($item->getId());
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 code(s) have been deleted', $count));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
