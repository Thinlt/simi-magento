<?php

namespace Vnecoms\PdfPro\Block;

use Vnecoms\PdfPro\Model\TemplateFactory as TemplateFactory;
use Magento\Framework\Registry;
use Vnecoms\PdfPro\Helper\Data as AdvancedHelper;

/**
 * Class Js.
 */
class Js extends \Magento\Framework\View\Element\Template
{
    /**
     * @var AdvancedHelper
     */
    protected $_helper;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;
    /**
     * @var
     */
    protected $keyFactory;
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * @var \Vnecoms\PdfPro\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Vnecoms\PdfPro\Model\VariableFactory
     */
    protected $_variableFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    protected $_moduleReader;

    /**
     * Javascript constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context    $context
     * @param AdvancedHelper                                      $helper
     * @param Registry                                            $registry
     * @param TemplateFactory                                     $templateFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config                   $wysiwygConfig
     * @param \Vnecoms\PdfPro\Model\CategoryFactory $categoryFactory
     * @param \Vnecoms\PdfPro\Model\VariableFactory $variableFactory
     * @param \Magento\Backend\Model\UrlInterface                 $backendUrl
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\PdfPro\Helper\Data $helper,
        Registry $registry,
        TemplateFactory $templateFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Vnecoms\PdfPro\Model\CategoryFactory $categoryFactory,
        \Vnecoms\PdfPro\Model\VariableFactory $variableFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->coreRegistry = $registry;
        $this->templateFactory = $templateFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_categoryFactory = $categoryFactory;
        $this->_variableFactory = $variableFactory;
        $this->_backendUrl = $backendUrl;
        $this->_moduleReader = $moduleReader;
    }

    /**
     * @return bool| \Vnecoms\PdfPro\Model\Template
     */
    public function getCurrentTemplate()
    {
        if ($apiKey = $this->coreRegistry->registry('current_key')) {
            $template = $this->templateFactory->create()->load($apiKey->getTemplateId());

            return $template;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        if ($template = $this->coreRegistry->registry('current_key')) {
            if ($template->getLogo()) {
                return $this->_helper->getBaseUrlMedia('ves_pdfpro/logos/'.$template->getLogo());
            }
        }

        return $this->_assetRepo->getUrl('Vnecoms_PdfPro::images/logo_bg.gif');
    }

    /**
     * Get TinyMCE Editor configuration.
     */
    public function getEditorConfigJSON()
    {
        $config = $this->_wysiwygConfig->getConfig(['tab_id' => 'form_section']);

        return \Zend_Json::encode($config);
    }

    /**
     * get default CSS URL.
     *
     * @return string
     */
    public function getDefaultCssUrl()
    {
        return $this->_assetRepo->getUrl('Vnecoms_PdfPro::templates/default.css');
    }

    /**
     * get BarCode Image URL.
     *
     * @return string
     */
    public function getBarCodeImageUrl()
    {
        return $this->_assetRepo->getUrl('Vnecoms_PdfPro::images/barcode.png');
    }
}
