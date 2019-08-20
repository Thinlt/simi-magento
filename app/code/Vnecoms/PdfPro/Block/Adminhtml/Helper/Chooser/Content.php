<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Helper\Chooser;

use Magento\Framework\Registry;

class Content extends \Magento\Backend\Block\Template
{
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var \Vnecoms\PdfPro\Model\TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;

    /**
     * @var string
     */
    protected $_template = 'key/helper/chooser.phtml';

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * Content constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Vnecoms\PdfPro\Helper\Data              $helper
     * @param \Vnecoms\PdfPro\Model\TemplateFactory    $templateFactory
     * @param Registry                                 $registry
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\TemplateFactory $templateFactory,
        Registry $registry,
        \Magento\Backend\Model\UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_helper = $helper;
        $this->templateFactory = $templateFactory;
        $this->coreRegistry = $registry;
        $this->_backendUrl = $urlInterface;

        parent::__construct($context, $data);
    }

    public function getTemplateJson($toJson = true)
    {
        $collection = $this->templateFactory->create()->getCollection();
        $data = [];
        foreach ($collection as $template) {
            $data[$template->getId()] = $template->getData();
            $data[$template->getId()]['css_url'] = $this->_helper->getBaseUrlMedia($template->getCssPath());
            if ($template->getPreviewImage()) {
                $data[$template->getId()]['preview_url'] =
                    $this->_helper->getBaseUrlMedia($template->getPreviewImage());
            } else {
                $data[$template->getId()]['preview_url'] =
                    $this->_helper->getBaseUrlMedia('ves_pdfpro/templates/default-preview.jpg');
            }
        }
        if ($toJson) {
            return json_encode($data);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getTypeJSON()
    {
        return json_encode(array('order_template', 'invoice_template', 'shipment_template', 'creditmemo_template'));
    }

    /**
     * @return bool
     */
    public function getCurrentTemplate()
    {
        if ($apiKey = $this->coreRegistry->registry('current_key')) {
            $template = $this->templateFactory->create()->load($apiKey->getTemplateId());

            return $template;
        }

        return false;
    }

    public function getLogoUrl()
    {
        if ($template = $this->coreRegistry->registry('current_key')) {
            if ($template->getLogo()) {
                return $this->_helper->getBaseUrlMedia($template->getLogo());
            }
        }

        return $this->_assetRepo->getUrl('Vnecoms_PdfPro::images/logo_bg.gif');
    }

    public function getDefaultCssUrl()
    {
        return $this->_assetRepo->getUrl('Vnecoms_PdfPro::templates/default.css');
    }

    public function getBarCodeImageUrl()
    {
        return $this->_assetRepo->getUrl('Vnecoms_PdfPro::images/barcode.png');
    }

    public function getAjaxUrl()
    {
        return $this->_backendUrl->getUrl('vnecoms_pdfpro/key/loadTemplate');
    }

    /**
     * @return \Vnecoms\PdfPro\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return string
     */
    public function getPreviewImagesJson()
    {
        $collection = $this->templateFactory->create()->getCollection();
        $data = [];
        foreach ($collection as $template) {
            if ($template->getPreviewImage()) {
                $data[$template->getId()] = [
                    'image' => $this->_helper->getBaseUrlMedia($template->getPreviewImage()),
                    'label' => $template->getName(),
                ];
            } else {
                $data[$template->getId()] = [
                    'image' => $this->_helper->getBaseUrlMedia('ves_pdfpro/templates/default-preview.jpg'),
                    'label' => $template->getName(),
                ];
            }
        }

        return \Zend_Json::encode($data);
    }
}
