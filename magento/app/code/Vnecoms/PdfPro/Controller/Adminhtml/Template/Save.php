<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

use Magento\Framework\Registry;
use Vnecoms\PdfPro\Controller\Adminhtml\Template;
use Vnecoms\PdfPro\Model\TemplateFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js as JsHelper;

/**
 * Class Save.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Save extends Template
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * Save constructor.
     *
     * @param JsHelper                                         $jsHelper
     * @param Registry                                         $registry
     * @param TemplateFactory                                  $templateFactory
     * @param \Magento\Framework\View\Result\LayoutFactory     $resultLayoutFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param Context                                          $context
     */
    public function __construct(
        JsHelper $jsHelper,

        Registry $registry,
        TemplateFactory $templateFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        Context $context
    ) {
        $this->jsHelper = $jsHelper;
        parent::__construct($registry, $templateFactory, $resultLayoutFactory, $fileFactory, $context);
    }

    /**
     * run the action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->initTemplate();
            if (isset($data['upload_id']) and $data['upload_id'] != '') {
                try {
                    //unzip
                    $zip = new \ZipArchive();
                    $x = $zip->open($data['upload_id']);  // open the zip file to extract
                    $filename = basename($data['upload_id'], '.zip');
                    if ($x === true) {
                        $zip->extractTo($data['target_dir'].'/'); // place in the directory with same name
                        $zip->close();

                        unlink($data['upload_id']);
                    }
                    
                    $xml = new \Zend_Config_Xml($data['target_dir'].'/'.$filename.'.xml');
                    $xml_array = $xml->toArray();
                    $sku = $xml_array['name'];
                    $css_path = $xml_array['css_path'];
                    $order_template = $xml_array['order_template'];
                    $invoice_template = $xml_array['invoice_template'];
                    $shipment_template = $xml_array['shipment_template'];
                    $creditmemo_template = $xml_array['creditmemo_template'];

                    //this way the name is saved in DB
                    $data['sku'] = $sku;
                    $data['css_path'] = $css_path;
                    $data['order_template'] = $order_template;
                    $data['invoice_template'] = $invoice_template;
                    $data['shipment_template'] = $shipment_template;
                    $data['creditmemo_template'] = $creditmemo_template;
                    $data['preview_image'] = 'ves_pdfpro/templates/'.$filename.'/preview.jpg';

                    unlink($data['target_dir'].'/'.$filename.'.xml');
                } catch (\Exception $e) {
                    echo $e->getMessage();exit;
                    $this->messageManager->addError($e->getMessage());
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                    return;
                }
            } else {
                $skuData = $model->load($this->getRequest()->getParam('id'))->getSku();
                if ($skuData) {
                    $data['sku'] = $skuData;
                    $data['css_path'] = $model->load($this->getRequest()->getParam('id'))->getCssPath();
                }
            }
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();

                $this->messageManager->addSuccess(__('Theme was successfully saved'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;
            }
        }
        //redirect index if not found anything
        $this->messageManager->addError(__('Unable to find Theme to save'));
        $this->_redirect('*/*/index');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::theme');
    }
}
