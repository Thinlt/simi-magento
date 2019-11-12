<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Guide;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action {

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(\Magento\Backend\App\Action\Context $context) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        /* @var \Magento\Framework\View\Result\Page $resultLayout */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $resultPage;
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi_Simistorelocator::guide');
    }

}
