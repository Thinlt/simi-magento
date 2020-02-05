<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Key;

/**
 * Class BuildWidget.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class SaveTemplate extends \Magento\Backend\App\Action
{
    /**
     * @var \Vnecoms\PdfPro\Model\TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Vnecoms\PdfPro\Model\TemplateFactory $templateFactory,
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->templateFactory = $templateFactory;
        $this->helper = $helper;
    }

    /**
     * Format widget pseudo-code for inserting into wysiwyg editor.
     */
    public function execute()
    {
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
