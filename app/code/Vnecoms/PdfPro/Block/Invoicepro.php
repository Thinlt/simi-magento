<?php

/** @noinspection ProblematicWhitespace */

namespace Vnecoms\PdfPro\Block;

use Vnecoms\PdfPro\Model\TemplateFactory as TemplateFactory;
use Magento\Framework\Registry;
use Vnecoms\PdfPro\Helper\Data as AdvancedHelper;
use Magento\Framework\DataObject;
use Vnecoms\PdfPro\Model\KeyFactory;

/**
 * Class Invoicepro.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Invoicepro extends \Magento\Backend\Block\Template
{
    /**
     * @var
     */
    protected $_api_keys;

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
     * @var KeyFactory
     */
    protected $keyFactory;

    /**
     * @var \Vnecoms\PdfPro\Model\Template\Filter
     */
    protected $processorFilter;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * Invoicepro constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Vnecoms\PdfPro\Model\Template\Filter   $processorFilter
     * @param AdvancedHelper                          $helper
     * @param Registry                                $registry
     * @param TemplateFactory                         $templateFactory
     * @param KeyFactory                              $keyFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Vnecoms\PdfPro\Model\Template\Filter $processorFilter,
        AdvancedHelper $helper,
        Registry $registry,
        \Vnecoms\PdfPro\Model\TemplateFactory $templateFactory,
        \Vnecoms\PdfPro\Model\KeyFactory $keyFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_helper = $helper;
        $this->templateFactory = $templateFactory;
        $this->coreRegistry = $registry;
        $this->keyFactory = $keyFactory;
        $this->processorFilter = $processorFilter;
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('template-pro.phtml');
        parent::_prepareLayout();
    }

    /**
     * @param string $fileName
     *
     * @return mixed
     */
    public function getSkinDir($fileName = '')
    {
        return $this->_helper->getBaseDirWeb($fileName);
    }

    /**
     * get default template css url.
     *
     * @return string
     */
    public function getDefaultCss()
    {
        return $this->_helper->getBaseUrlMedia('ves_pdfpro/templates/default.css');
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getApiKeyInformation($key)
    {
        return $this->keyFactory->create()->load($key, 'api_key')->getData();
    }

    /**
     * @param $media
     *
     * @return mixed
     */
    public function getMediaUrl($media)
    {
        return $this->_helper->getBaseUrlMedia($media);
    }

    /**
     * Get body html.
     *
     * @param string $html
     */
    public function getBody($html)
    {
        $html = preg_replace('/<div class=\"header-pdf\">(.*?)<\/div>/si', '', $html);
        $html = preg_replace('/<div class=\"footer-pdf\">(.*?)<\/div>/si', '', $html);

        return $html;
    }

    /**
     * @param $html
     *
     * @return string
     */
    public function getHeader($html)
    {
        preg_match('/<div class=\"header-pdf\">(.*?)<\/div>/si', $html, $matches);
        if ($matches && (sizeof($matches) == 2)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param $html
     *
     * @return string
     */
    public function getFooter($html)
    {
        preg_match('/<div class=\"footer-pdf\">(.*?)<\/div>/si', $html, $matches);
        if ($matches && (sizeof($matches) == 2)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param $_invoice
     *
     * @return mixed
     */
    public function processTemplate($_invoice)
    {
        $apiKey = $_invoice['key'];
        $data = $_invoice['data'];
        if ($this->getType() != 'order') {
            $data['order'] = $data['order']['data'];
        }
        if ($this->getType() == 'custom1' or $this->getType() == 'custom2' or $this->getType() == 'custom3') {
            $order = $this->_objectManager->create('Magento\Sales\Api\OrderRepositoryInterface')->get($data->getData('entity_id'));
            $orderData = $this->_objectManager->create('Vnecoms\PdfPro\Model\Order')->initOrderData($order);
            $data['order'] = unserialize($orderData);
        }

        $apiKey = $this->getApiKeyInfo($_invoice['key']);//var_dump($_invoice['key']);
        $pdfProApiKey = $this->keyFactory->create()->load($_invoice['key'], 'api_key'); //var_dump($pdfProApiKey->getData());die();
        $logoUrl = $this->_helper->getBaseUrlMedia('ves_pdfpro/logos/'.$pdfProApiKey->getLogo());

        /* Get domain config */
        if ($apiKey->getConfig()) {
            $additionData = json_decode($apiKey->getConfig(), true);
        } else {
            $additionData = array();
        }
    //	die();

        //var_dump($additionData);die();
        $config = new DataObject($additionData);

    //	die();
        /* Process the body of PDF */
        $customer = new DataObject($data->getCustomer());
        $variables = array('system' => new DataObject($this->_helper->getTaxDisplayConfig()),
            'type' => $this->getType(), $this->getType() => $data,
            'customer' => $customer,
            'billing' => $data['billing'],
            'shipping' => $data['shipping'],
            'payment' => new DataObject($data['payment']),
            'NUMBER_OF_INVOICE' => count($data), 'config' => $config,
            'MY_LOGO' => '<img src="'.$logoUrl.'" />', );
        if ($this->getType() != 'order') {
            $variables['order'] = $data['order'];
        }
        if ($this->getType() == 'custom1' or $this->getType() == 'custom2' or $this->getType() == 'custom3') {
            $variables['order'] = $data['order']->getData('data');
        }

     //   var_dump($variables['MY_LOGO']);die();

        $this->processorFilter->setVariables($variables);

        $template = $apiKey->getData($this->getType().'_template');
        $template = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $template);

        return $this->processorFilter->filter($template);
    }

    /**
     * get css urls of template Model.
     *
     * @return array
     */
    public function getCssUrls()
    {
        $cssUrls = array();
        foreach ($this->getApiKeys() as $index => $key) {
            $templateId = $key->getTemplateId();
            $cssUrl = $this->templateFactory->create()->load(['id' => $templateId])->getCssPath();
            $cssUrls[] = $this->_helper->getBaseUrlMedia($cssUrl);
        }

        return $cssUrls;
    }

    /**
     * get Additional CSS each API.
     *
     * @return string
     */
    public function getAdditionCss()
    {
        $css = '';
        foreach ($this->getApiKeys() as $key) {
            $css .= $key->getAdditionCss();
        }

        return $css;
    }

    /**
     * get css in DB
     * ->css addition from user add into each api_key.
     *
     * @return string
     */
    public function getCss()
    {
        $css = '';
        foreach ($this->getApiKeys() as $api_key => $key) {
            $css .= $key->getCss();
        }

        return $css;
    }

    /**
     * get invoice SKU of API KEY.
     *
     * @param $apiKey
     *
     * @return string
     */
    public function getInvoiceSku($apiKey)
    {
        foreach ($this->getApiKeys() as $index => $key) {
            if ($apiKey == $index) {
                $templateId = $key->getTemplateId();//var_dump($apiKey);
                return $this->templateFactory->create()->load($templateId)->getSku();
            }
        }
    }

    /**
     * @param $apiKey
     *
     * @return mixed
     */
    public function getApiKeyInfo($apiKey)
    {
        $apiKeys = $this->getApiKeys();

        return $apiKeys[$apiKey];
    }

    /**
     * get additional data of api key.
     *
     * @param $apiKey
     *
     * @return string
     */
    public function getAdditionData($apiKey)
    {
        $apiKeys = $this->getApiKeys();
        $additionData = json_decode($apiKeys[$apiKey]->getConfig(), true);

        return $additionData;
    }
}
