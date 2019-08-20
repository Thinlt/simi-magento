<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

/**
 * Class Grid.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Grid extends \Vnecoms\PdfPro\Controller\Adminhtml\Template
{
    /**
     * set page data.
     *
     * @return $this
     */
    public function setPageData()
    {
        return $this;
    }

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::theme');
    }
}
