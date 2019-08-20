<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\Giftcardcodes;

use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassAbstract
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
abstract class MassAbstract extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::giftcard';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GiftcardRepositoryInterface
     */
    protected $giftcardRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param GiftcardRepositoryInterface $giftcardRepository
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        GiftcardRepositoryInterface $giftcardRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->giftcardRepository = $giftcardRepository;
    }

    /**
     * Run mass action
     *
     * @return $this
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }

    /**
     * Performs mass action
     *
     * @param CollectionFactory $collection
     * @return void
     */
    abstract protected function massAction($collection);
}
