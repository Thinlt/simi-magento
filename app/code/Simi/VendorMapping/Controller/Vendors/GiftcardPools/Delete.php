<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\GiftcardPools;

use Magento\Framework\View\Result\PageFactory;
use Aheadworks\Giftcard\Api\PoolRepositoryInterface;
use Vnecoms\Vendors\App\Action\Context;

/**
 * Class Delete
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Pool
 */
class Delete extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::giftcard_pools';

    /**
     * @var PoolRepositoryInterface
     */
    private $poolRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PoolRepositoryInterface $poolRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PoolRepositoryInterface $poolRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->poolRepository = $poolRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($poolId = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->poolRepository->deleteById($poolId);
                $this->messageManager->addSuccessMessage(__('Code pool was successfully deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Code pool could not be deleted'));
        return $resultRedirect->setPath('*/*/');
    }
}
