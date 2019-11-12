<?php

namespace Simi\Simistorelocator\Controller\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;

abstract class AbstractExportAction extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $fileFactory;

    /**
     * @param Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export store grid to Excel XML format.
     *
     * @return ResponseInterface
     */
    public function execute() {
        return $this->fileFactory->create(
                        $this->_getFileName(), $this->_getContent(), DirectoryList::VAR_DIR
        );
    }

    /**
     * Check if admin has permissions to visit related pages.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi\Simistorelocator::storelocator');
    }

    /**
     * file name to export.
     *
     * @return string
     */
    abstract protected function _getFileName();

    /**
     * content to export file.
     *
     * @return string
     */
    abstract protected function _getContent();
}
