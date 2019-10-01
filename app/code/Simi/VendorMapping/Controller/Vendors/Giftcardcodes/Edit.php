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
use Vnecoms\Vendors\Model\Session;

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
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $vendorSession;

    /**
     * @param Context $context
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        GiftcardRepositoryInterface $giftcardRepository,
        PageFactory $resultPageFactory,
        Session $vendorSession
    ) {
        parent::__construct($context);
        $this->giftcardRepository = $giftcardRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->vendorSession = $vendorSession;
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
                $giftcard = $this->giftcardRepository->get($giftcardId);
                if ($giftcard->getVendorId() != $this->vendorSession->getVendor()->getVendorId()) {
                    throw new NoSuchEntityException(__('This gift card no longer exists.'));
                }
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
