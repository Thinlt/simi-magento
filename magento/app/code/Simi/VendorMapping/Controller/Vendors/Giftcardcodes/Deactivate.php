<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\Giftcardcodes;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Vnecoms\Vendors\App\Action\Context;

/**
 * Class Deactivate
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class Deactivate extends \Vnecoms\Vendors\Controller\Vendors\Action
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
     * Deactivate action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($giftcardId = (int)$this->getRequest()->getParam('id')) {
            try {
                $giftcardCode = $this->giftcardRepository->get($giftcardId);
                $giftcardCode->setState(Status::DEACTIVATED);
                $this->giftcardRepository->save($giftcardCode);
                $this->messageManager->addSuccessMessage(__('Gift Card code was successfully deactivated'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deactivating Gift Card code')
                );
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
