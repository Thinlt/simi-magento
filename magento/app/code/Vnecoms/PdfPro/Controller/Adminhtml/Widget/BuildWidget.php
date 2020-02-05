<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Widget;

/**
 * Class BuildWidget.
 */
class BuildWidget extends \Magento\Backend\App\Action
{
    /**
     * @var \VnEcoms\AdvancedPdfProcessor\Model\Widget
     */
    protected $_widget;

    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \VnEcoms\AdvancedPdfProcessor\Model\Widget $widget
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Vnecoms\PdfPro\Model\Widget $widget
    ) {
        $this->_widget = $widget;
        parent::__construct($context);
    }

    /**
     * Format widget pseudo-code for inserting into wysiwyg editor.
     */
    public function execute()
    {
        $type = $this->getRequest()->getPost('widget_type');
        $params = $this->getRequest()->getPost('parameters', []);
        $asIs = $this->getRequest()->getPost('as_is');
        $html = $this->_widget->getWidgetDeclaration($type, $params, $asIs);
        $this->getResponse()->setBody($html);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
