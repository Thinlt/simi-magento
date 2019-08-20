<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\Giftcardcodes;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Vnecoms\Vendors\App\Action\Context;

/**
 * Class Edit
 *
 * @package Simi\VendorMapping\Controller\Vendors\Giftcardcodes;
 */
class Edit extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::giftcard_codes';

    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        GiftcardRepositoryInterface $giftcardRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->giftcardRepository = $giftcardRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_initAction();
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $this->setActiveMenu($this->_aclResource);
        $giftcardId = (int)$this->getRequest()->getParam('id');
        if ($giftcardId) {
            try {
                $this->giftcardRepository->get($giftcardId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This gift card no longer exists.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        $prependTitle = $giftcardId ? __('Edit Gift Card Code') : __('New Gift Card Code');
        $title->prepend($prependTitle);
        $title->prepend(__("Manage Gift Voucher"));
        $this->_addBreadcrumb($prependTitle, $prependTitle)->_addBreadcrumb(__("Manage Gift Voucher"), __("Manage Gift Voucher"));
        $this->_view->renderLayout();
    }
}
