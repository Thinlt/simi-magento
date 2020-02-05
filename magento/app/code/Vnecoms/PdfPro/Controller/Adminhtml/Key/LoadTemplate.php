<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Key;

/**
 * Class BuildWidget.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class LoadTemplate extends \Magento\Backend\App\Action
{
    protected $templateFactory;

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
        $id = $this->getRequest()->getPost('id');
        $template = $this->templateFactory->create()->load($id);
        $data = $template->getData();
        $data['pdfpro_key_form_order_template'] = $data['order_template'];
        $data['pdfpro_key_form_invoice_template'] = $data['invoice_template'];
        $data['pdfpro_key_form_shipment_template'] = $data['shipment_template'];
        $data['pdfpro_key_form_creditmemo_template'] = $data['creditmemo_template'];

        unset($data['order_template']);
        unset($data['invoice_template']);
        unset($data['shipment_template']);
        unset($data['creditmemo_template']);
        $data['css_url'] = $this->helper->getBaseUrlMedia($template->getCssPath());

        $this->getResponse()->setBody(\Zend_Json_Encoder::encode($data));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
