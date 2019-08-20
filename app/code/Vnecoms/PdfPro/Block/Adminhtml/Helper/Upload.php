<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Helper;

use Magento\Framework\Data\Form\Element\AbstractElement as AbstractElement;
use Magento\Framework\Escaper;

/**
 * Class Uploader.
 *
 * @author VnEcoms team <vnecoms.com>
 */
class Upload extends AbstractElement
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_config;

    /**
     * Asset service.
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Url Builder.
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        Escaper $escaper,
        array $data
    ) {
        $this->_helper = $helper;
        $this->_urlBuilder = $urlInterface;
        $this->pageConfig = $pageConfig;
        $this->_asssetRepo = $assetRepository;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId($this->getId().'_Uploader');

        $uploadUrl = $this->_urlBuilder->addSessionParam()->getUrl('vnecoms_pdfpro/template/upload');
        $this->getConfig()->setUrl($uploadUrl);
        $this->getConfig()->setFileField('file');
    }

    public function getJsObjectName()
    {
        return $this->getHtmlId().'JsObject';
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->addPageAsset('jquery/fileUploader/css/jquery.fileupload-ui.css');

        return parent::_prepareLayout();
    }

    public function getElementHtml()
    {
        $block = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Framework\View\Element\Template');
        $block
            ->setHtmlId($this->getHtmlId())
            ->setLimitUpload($this->getLimitUpload())
            ->setExtensionsJson($this->getExtensionsJSON())
            ->setMaxSize($this->getMaxSize())
            ->setUploaderUrl($this->getUploaderUrl())
            ->setTemplate('Vnecoms_PdfPro::template/edit/tab/form/renderer/attachments_filer.phtml')
            //  ->setUrl($this->getUrl('vendors/pricecomparison/upload'))
        ;
        $html = $block->toHtml();
        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * @return string
     */
    public function getLimitUpload()
    {
        return $this->_helper->getConfig('pdfpro/upload/limit');
    }

    /**
     * @return string
     */
    public function getExtensionsJSON()
    {
        return json_encode($this->_helper->getConfig('pdfpro/upload/extensions'));
    }

    /**
     * @return string
     */
    public function getMaxSize()
    {
        return $this->_helper->getConfig('pdfpro/upload/max_size');
    }

    public function getUploaderUrl()
    {
        return $this->_urlBuilder->getUrl('vnecoms_pdfpro/template/upload').'?isAjax=true';
    }

    /**
     * Retrieve config object.
     *
     * @return \Magento\Framework\DataObject
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = new \Magento\Framework\DataObject();
        }

        return $this->_config;
    }

    /**
     * Retrieve config json.
     *
     * @return string
     */
    public function getConfigJson()
    {
        return json_encode($this->getConfig()->getData());
    }
}
