<?php

namespace Vnecoms\PdfPro\Model\Processors;

use Vnecoms\PdfPro\Model\KeyFactory;
use Magento\Framework\App\State as AppState;
use Magento\Framework\DataObject;
use Magento\Framework\App\Area;
use Mpdf\Mpdf as CoreMpdf;

/**
 * Class Mpdf.
 */
class Mpdf extends \Magento\Framework\DataObject
{
    /**
     * @var KeyFactory
     */
    protected $_keyFactory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Vnecoms\PdfPro\Helper\Mconfig
     */
    protected $_pdfConfig;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * Mpdf constructor.
     * @param KeyFactory $keyFactory
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param AppState $appState
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Vnecoms\PdfPro\Helper\Mconfig $config
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        KeyFactory $keyFactory,
        \Vnecoms\PdfPro\Helper\Data $helper,
        AppState $appState,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\LayoutInterface $layout,
        \Vnecoms\PdfPro\Helper\Mconfig $config,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct($data);
        $this->_keyFactory = $keyFactory;
        $this->_helper = $helper;
        $this->appState = $appState;
        $this->_logger = $logger;
        $this->_layout = $layout;
        $this->_pdfConfig = $config;
        $this->directoryList = $directoryList;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    /**
     * @param $apiKey
     *
     * @return array
     */
    public function getInfo($apiKey)
    {
        return $this->_keyFactory->create()->load($apiKey, 'api_key')->getData();
    }

    /**
     * @param string $apiKey
     * @param $datas
     * @param $type
     *
     * @return array
     *
     * @throws \Exception
     */
    public function process($apiKey, $datas, $type)
    {
        //get config tax
        $config = $this->_helper->getTaxDisplayConfig();

        /*Get API Key information*/
        $apiKeyInfo = $this->getInfo($apiKey); //get info of api (css, template, sku)


        if ($type == 'all') {
            return $this->processAllPdf($datas, $apiKey);
        }    //check type of invoice(order,invoice....)
        $sources = array();
        $apiKeys = array();    /*store all api key*/

        foreach ($datas as $data) {
            $tmpData = ($data);

            /*Get API Key information*/
            $pdfInfo = $this->getInfo($tmpData['key']);// var_dump($pdfInfo);die();

            if (!is_array($pdfInfo) || !isset($pdfInfo[$type.'_template'])) {
                $errMsg = __('Your API key is not valid.');
                if ($this->appState->getAreaCode() == Area::AREA_ADMINHTML) {
                    throw new \Exception($errMsg);
                } else {
                    throw new \Exception(__('Can not generate PDF file. Please contact administrator about this error.'));
                }
            }

            if (!isset($apiKeys[$tmpData['key']])) {
                $apiKeys[$tmpData['key']] = new DataObject($pdfInfo);
            }
            $sources[] = $tmpData;
        }

        $this->_pdfConfig->loadPdfConfig();

        $pageSize = $this->_helper->getConfig('pdfpro/advanced/page_size');
        $orientation = $this->_helper->getConfig('pdfpro/advanced/orientation');
        $autolang = $this->_helper->getConfig('pdfpro/advanced/autolang');

        //mode
        //format
        //default font size
        //default font
        //margin-left
        //margin rght
        //margin top
        //margin bottom
        //margin header, footer, orientation
        if ($autolang == \Vnecoms\PdfPro\Model\Source\Lang::CORE) {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'c',
                'format' => $pageSize,
                'orientation' => $orientation,
                'tempDir' => $this->directoryList->getPath('var').'/tmp'
            ]);
        } elseif ($autolang == \Vnecoms\PdfPro\Model\Source\Lang::ALL) {
            /*auto langs*/
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => $pageSize,
                'orientation' => $orientation,
                'tempDir' => $this->directoryList->getPath('var').'/tmp'
            ]);

            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;
        }

        $mpdf->SetTitle($this->_helper->getConfig('pdfpro/general/pdf_title'));
        $mpdf->SetAuthor($this->_helper->getConfig('pdfpro/general/pdf_author'));

	    $mpdf->shrink_tables_to_fit = 1; // move table to next page if it not fit
        $mpdf->simpleTables = true; //reduce memory
        $mpdf->packTableData = true;//reduce memory
        $mpdf->useSubstitutions = false;
        $mpdf->SetProtection(array('print'),'',$this->_helper->getConfig('pdfpro/advanced/password_protection'));
        $mpdf->SetDisplayMode('fullpage');
	    $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->mirrorMargins = true;

        $fontData = [];
        $fontDataMerge = [];
        if (isset($apiKeyInfo['font_data'])) {
            if (isset($apiKeyInfo['font_data'])
                && is_string($apiKeyInfo['font_data'])
                && class_exists(\Magento\Framework\Serialize\Serializer\Json::class)) {
                $fontData = $this->serializer->unserialize($apiKeyInfo['font_data']);
            } elseif (isset($apiKeyInfo['font_data']) && !is_string($apiKeyInfo['font_data'])) {
                $fontData = unserialize($apiKeyInfo['font_data']);
            }
            $fontDataMerge = [];
            foreach ($fontData as $font) {
                $r = [
                    'R' => $font['regular_font'],
                    'B' => $font['regular_font'],
                    'I' => $font['i_font'],
                    'BI' => $font['regular_font'],
                ];
                if ($font['otl']) {
                    $r['useOTL'] = 0xFF;
                }
                $fontDataMerge[$font['title']] = $r;
            }
        }

        if ($fontDataMerge && is_array($fontDataMerge))
            $mpdf->fontdata = array_merge($mpdf->fontdata, $fontDataMerge);

        $mpdf->available_unifonts = array();
        foreach ($mpdf->fontdata AS $f => $fs) {
            if (isset($fs['R']) && $fs['R']) {
                $mpdf->available_unifonts[] = $f;
            }
            if (isset($fs['B']) && $fs['B']) {
                $mpdf->available_unifonts[] = $f . 'B';
            }
            if (isset($fs['I']) && $fs['I']) {
                $mpdf->available_unifonts[] = $f . 'I';
            }
            if (isset($fs['BI']) && $fs['BI']) {
                $mpdf->available_unifonts[] = $f . 'BI';
            }
        }

        $mpdf->default_available_fonts = $mpdf->available_unifonts;

        $block = \Magento\Framework\App\ObjectManager::getInstance()->create('Vnecoms\PdfPro\Block\PdfBuilder');
        $block->setData(array('config' => $config, 'source' => $sources, 'type' => $type, 'api_keys' => $apiKeys));
        $block->setArea('adminhtml')->setIsSecureMode(true);
        $block->setTemplate('Vnecoms_PdfPro::template-builder.phtml');

        $block->setPdf($mpdf);

        $html = preg_replace('/>\s+</', '><', $block->toHtml());
        $html = htmlspecialchars_decode($html);
        $mpdf->WriteHTML($html);
        $content = $mpdf->Output('', 'S');
        return array('success' => true, 'content' => $content);
    }

    /**
     * @param $datas
     * @param $apiKey
     */
    public function processAllPdf($datas, $apiKey)
    {
        return;
    }
}
