<?php

namespace Simi\VendorMapping\Controller\Vendors\GiftcardPools;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Giftcard\Api\PoolRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \Vnecoms\Vendors\Controller\Vendors\Action
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
        \Vnecoms\Vendors\App\Action\Context $context,
        PoolRepositoryInterface $poolRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->poolRepository = $poolRepository;
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
        $poolId = (int)$this->getRequest()->getParam('id');
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $breadCrumbTitle = $poolId ? __('Edit Code Pool') : __('New Code Pool');
        $this->setActiveMenu($this->_aclResource);
        $title->prepend($breadCrumbTitle);
        $title->prepend(__("Manage Gift Card Pools"));
        $this->_addBreadcrumb($breadCrumbTitle, $breadCrumbTitle)
            ->_addBreadcrumb(__("Manage Gift Card Pools"), __("Manage Gift Card Pools"));

        $poolId = (int)$this->getRequest()->getParam('id');
        if ($poolId) {
            try {
                $this->poolRepository->get($poolId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This pool no longer exists')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        $this->_view->renderLayout();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        // $resultPage = $this->resultPageFactory->create();
        // return $resultPage;
    }
}
