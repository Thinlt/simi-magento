<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\GiftcardPools\Code;

use Aheadworks\Giftcard\Api\PoolCodeRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Vnecoms\Vendors\App\Action\Context;

/**
 * Class Delete
 *
 * @package Simi\VendorMapping\Controller\Vendors\GiftcardPools\Code
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
     * @var PoolCodeRepositoryInterface
     */
    private $poolCodeRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PoolCodeRepositoryInterface $poolCodeRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PoolCodeRepositoryInterface $poolCodeRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->poolCodeRepository = $poolCodeRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($codeId = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->poolCodeRepository->deleteById($codeId);
                $this->messageManager->addSuccessMessage(__('Code was successfully deleted'));
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
