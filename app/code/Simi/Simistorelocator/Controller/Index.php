<?php

namespace Simi\Simistorelocator\Controller;

use Magento\Framework\Controller\ResultFactory;

abstract class Index extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Simi\Simistorelocator\Model\SystemConfig
     */
    public $systemConfig;

    /**
     * @var \Simi\Simistorelocator\Model\ResourceModel\Store\CollectionFactory
     */
    public $storeCollectionFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Action constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Simi\Simistorelocator\Model\SystemConfig $systemConfig,
        \Simi\Simistorelocator\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        parent::__construct($context);
        $this->systemConfig = $systemConfig;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function _getResultRedirectNoroute() {
        /* @var \Magento\Framework\Controller\Result\Redirect $resultLayout */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('cms/noroute');

        return $resultRedirect;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initResultPage() {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__($this->systemConfig->getPageTitpe()));

        return $resultPage;
    }
}
