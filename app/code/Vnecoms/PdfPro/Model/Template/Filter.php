<?php

namespace Vnecoms\PdfPro\Model\Template;

use CodeItNow\BarcodeBundle\Utils\QrCode;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

/**
 * Class Filter.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Filter extends \Magento\Email\Model\Template\Filter
{
    const CONSTRUCTION_ADVANCED_IF_PATTERN = '/{{if \(\s*(.*?)\s*\)\s*}}(.*?)({{else}}(.*?))?{{\\/if\s*}}/si';
    const CONSTRUCTION_ADVANCED_IF1_PATTERN = '/{{if1 \(\s*(.*?)\s*\)\s*}}(.*?)({{else1}}(.*?))?{{\\/if1\s*}}/si';
    const CONSTRUCTION_ADVANCED_IF2_PATTERN = '/{{if2 \(\s*(.*?)\s*\)\s*}}(.*?)({{else2}}(.*?))?{{\\/if2\s*}}/si';
    const CONSTRUCTION_ADVANCED_IF3_PATTERN = '/{{if3 \(\s*(.*?)\s*\)\s*}}(.*?)({{else3}}(.*?))?{{\\/if3\s*}}/si';
    const CONSTRUCTION_IF1_PATTERN = '/{{if1 (.*?)}}(.*?)({{else1}}(.*?))?{{\\/if1\s*}}/si';
    const CONSTRUCTION_IF2_PATTERN = '/{{if2 (.*?)}}(.*?)({{else2}}(.*?))?{{\\/if2\s*}}/si';
    const CONSTRUCTION_IF3_PATTERN = '/{{if3 (.*?)}}(.*?)({{else3}}(.*?))?{{\\/if3\s*}}/si';
    const CONSTRUCTION_FOREACH_PATTERN = '/{{foreach\s*(.*?)\s*as\s*(.*?)\s*}}(.*?){{\\/foreach\s*}}/si';
    const CONSTRUCTION_FOREACH_KEY_PATTERN = '/{{foreach\s*(.*?)\s*as\s*(.*?)\s*=>\s*(.*?)\s*}}(.*?){{\\/foreach\s*}}/si';
    const CONSTRUCTION_FOREACH1_PATTERN = '/{{foreach1\s*(.*?)\s*as\s*(.*?)\s*}}(.*?){{\\/foreach1\s*}}/si';
    const CONSTRUCTION_FOREACH1_KEY_PATTERN = '/{{foreach1\s*(.*?)\s*as\s*(.*?)\s*=>\s*(.*?)\s*}}(.*?){{\\/foreach1\s*}}/si';
    const CONSTRUCTION_VARDUMP_PATTERN = '/{{var_dump\s*(.*?)\s*}}/si';
    const CONSTRUCTION_INCREMENTING_PATTERN = '/{{inc (.*?) (.*?)}}/si';
    const CONSTRUCTION_EXPRESSION_PATTERN = '/{{expression\s*(.*?)}}/si';
    const CONSTRUCTION_BARCODE_PATTERN = '/{{barcode\s*(.*?)}}/si';
    const CONSTRUCTION_QRCODE_PATTERN = '/{{qrcode\s*(.*?)}}/si';
    const CONSTRUCTION_SET_VALUE_PATTERN = '/{{set \((.*?),(.*?)\)}}/si';
    const CONSTRUCTION_WIDGET_PATTERN = '/{{widget  (.*?)}}/si';

    protected $_offset = 0;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\Debug
     */
    protected $debug;
    
    /**
     * @var \Vnecoms\PdfPro\Block\Filter\Widget
     */
    protected $widgetBlock;

    /**
     * Filter constructor.
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Variable\Model\VariableFactory $coreVariableFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\UrlInterface $urlModel
     * @param \Pelago\Emogrifier $emogrifier
     * @param \Magento\Variable\Model\Source\Variables $configVariables
     * @param \Vnecoms\PdfPro\Block\Filter\Widget $widget
     * @param \Magento\Framework\Debug $debug
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param array $variables
     * @param \Magento\Framework\Css\PreProcessor\Adapter\CssInliner|null $cssInliner
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Variable\Model\VariableFactory $coreVariableFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\UrlInterface $urlModel,
        \Pelago\Emogrifier $emogrifier,
        \Magento\Variable\Model\Source\Variables $configVariables,
        \Vnecoms\PdfPro\Block\Filter\Widget $widget,
        \Magento\Framework\Debug $debug,
        \Vnecoms\PdfPro\Helper\Data $helper,
        array $variables = [],
        \Magento\Framework\Css\PreProcessor\Adapter\CssInliner $cssInliner = null
    ) {
        $this->helper = $helper;
        $this->debug = $debug;
        $this->layout = $layout;
        $this->widgetBlock = $widget;
        parent::__construct($string, $logger, $escaper, $assetRepo, $scopeConfig, $coreVariableFactory, $storeManager, $layout, $layoutFactory, $appState, $urlModel, $emogrifier, $configVariables, $variables, $cssInliner);
    }

    protected function _process($construction, $value)
    {
        switch ($construction[1]) {
            case 'var_dump':
                $replacedValue = $this->vardumpDirective($construction);
                break;
            case 'var':
                $replacedValue = $this->varDirective($construction);
                break;
            case 'barcode':
                preg_match(self::CONSTRUCTION_BARCODE_PATTERN, $value, $construction);
                $replacedValue = $this->barcodeDirective($construction);
                break;
            case 'qrcode':
                preg_match(self::CONSTRUCTION_QRCODE_PATTERN, $value, $construction);
                $replacedValue = $this->qrcodeDirective($construction);
                break;
            case 'if':
                if (preg_match('/{{if \(\s*(.*?)\s*\)\s*}}/si', $construction[0])) {
                    /* Advanced if */
                    preg_match(self::CONSTRUCTION_ADVANCED_IF_PATTERN, $value, $construction);
                    $replacedValue = $this->advancedIfDirective($construction);
                } else {
                    preg_match(self::CONSTRUCTION_IF_PATTERN, $value, $construction);
                    $replacedValue = $this->ifDirective($construction);
                }
                break;
            case 'if1':
                if (preg_match('/{{if1 \(\s*(.*?)\s*\)\s*}}/si', $construction[0])) {
                    /* Advanced if */
                    preg_match(self::CONSTRUCTION_ADVANCED_IF1_PATTERN, $value, $construction);
                    $replacedValue = $this->advancedIfDirective($construction);
                } else {
                    preg_match(self::CONSTRUCTION_IF1_PATTERN, $value, $construction);
                    $replacedValue = $this->ifDirective($construction);
                }
                break;
            case 'if2':
                if (preg_match('/{{if2 \(\s*(.*?)\s*\)\s*}}/si', $construction[0])) {
                    /* Advanced if */
                    preg_match(self::CONSTRUCTION_ADVANCED_IF2_PATTERN, $value, $construction);
                    $replacedValue = $this->advancedIfDirective($construction);
                } else {
                    preg_match(self::CONSTRUCTION_IF2_PATTERN, $value, $construction);
                    $replacedValue = $this->ifDirective($construction);
                }
                break;
            case 'if3':
                if (preg_match('/{{if3 \(\s*(.*?)\s*\)\s*}}/si', $construction[0])) {
                    /* Advanced if */
                    preg_match(self::CONSTRUCTION_ADVANCED_IF3_PATTERN, $value, $construction);
                    $replacedValue = $this->advancedIfDirective($construction);
                } else {
                    preg_match(self::CONSTRUCTION_IF3_PATTERN, $value, $construction);
                    $replacedValue = $this->ifDirective($construction);
                }
                break;
            case 'foreach':
                if (preg_match('/{{foreach\s*(.*?)\s*as\s*(.*?)\s*}}/si', $construction[0])) {
                    preg_match(self::CONSTRUCTION_FOREACH_PATTERN, $value, $construction);
                    $replacedValue = $this->foreachDirective($construction);
                } else {
                    preg_match(self::CONSTRUCTION_FOREACH_KEY_PATTERN, $value, $construction);
                    $replacedValue = $this->foreachDirective($construction);
                }
                break;
            case 'foreach1':
                if (preg_match('/{{foreach1\s*(.*?)\s*as\s*(.*?)\s*}}/si', $construction[0])) {
                    preg_match(self::CONSTRUCTION_FOREACH1_PATTERN, $value, $construction);
                    $replacedValue = $this->foreachDirective($construction);
                } else {
                    preg_match(self::CONSTRUCTION_FOREACH1_KEY_PATTERN, $value, $construction);
                    $replacedValue = $this->foreachDirective($construction);
                }
                break;
            case 'inc':
                preg_match(self::CONSTRUCTION_INCREMENTING_PATTERN, $value, $construction);
                $replacedValue = $this->incDirective($construction);
                break;
            case 'set':
                preg_match(self::CONSTRUCTION_SET_VALUE_PATTERN, $value, $construction);
                $replacedValue = $this->setDirective($construction);
                break;
            case 'widget':
                preg_match(self::CONSTRUCTION_WIDGET_PATTERN, $value, $construction);
                $replacedValue = $this->widgetDirective($construction);
                break;
        }
        $value = str_replace($construction[0], $replacedValue, $value);

        return $value;
    }

    public function advancedFilter($value)
    {
        $this->_offset = 0;
        $i = 0;
        while (true) {
            if ($i++ > 10) {
                break;
            }
            if (preg_match('/{{(.*?) (.*?)\s*}}/si', $value, $construction)) {
                $value = $this->_process($construction, $value);
            } else {
            }
        }

        return $value;
    }

    public function filter($value)
    {
        // "foreach" operand should be first
        foreach (array(
                     self::CONSTRUCTION_FOREACH_KEY_PATTERN => 'foreachDirectiveWithKey',
                     self::CONSTRUCTION_FOREACH_PATTERN => 'foreachDirective',
                     self::CONSTRUCTION_FOREACH1_KEY_PATTERN => 'foreachDirectiveWithKey',
                     self::CONSTRUCTION_FOREACH1_PATTERN => 'foreachDirective',
                     self::CONSTRUCTION_WIDGET_PATTERN => 'widgetDirective',
                     self::CONSTRUCTION_INCREMENTING_PATTERN => 'incrementDirective',
                     self::CONSTRUCTION_SET_VALUE_PATTERN => 'setDirective',
                     self::CONSTRUCTION_ADVANCED_IF_PATTERN => 'advancedIfDirective',
                     self::CONSTRUCTION_ADVANCED_IF1_PATTERN => 'advancedIfDirective',
                     self::CONSTRUCTION_ADVANCED_IF2_PATTERN => 'advancedIfDirective',
                     self::CONSTRUCTION_IF1_PATTERN => 'ifDirective',
                     self::CONSTRUCTION_IF2_PATTERN => 'ifDirective',
                     self::CONSTRUCTION_IF3_PATTERN => 'ifDirective',
                     self::CONSTRUCTION_VARDUMP_PATTERN => 'vardumpDirective',
                     self::CONSTRUCTION_BARCODE_PATTERN => 'barcodeDirective',
                     self::CONSTRUCTION_QRCODE_PATTERN => 'qrcodeDirective',
                 ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $index => $construction) {
                    $replacedValue = '';
                    $callback = array($this, $directive);
                    if (!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        return parent::filter($value);
    }

    public function ifDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }

        if (!$this->getVariable($construction[1], '')) {
            if (isset($construction[3]) && isset($construction[4])) {
                return $this->advancedFilter($construction[4]);
            }

            return '';
        } else {
            return $this->advancedFilter($construction[2]);
        }
    }

    public function foreachDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }
        $arr = $this->getVariable($construction[1], '');
        $replacedValue = '';
        if (is_array($arr)) {
            $templateVar = $this->templateVars;
            foreach ($arr as $value) {
                $this->templateVars[$construction[2]] = $value;
                $replacedValue .= $this->advancedFilter($construction[3]);
            }
            //unset($this->templateVars[$construction[2]]);
            return $replacedValue;
        } else {
            return $construction[0];
        }
    }

    public function foreachDirectiveWithKey($construction)
    {
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }
        $arr = $this->getVariable($construction[1], '');
        $replacedValue = '';
        if (is_array($arr)) {
            $templateVar = $this->templateVars;
            foreach ($arr as $key => $value) {
                $this->templateVars[$construction[2]] = $key;
                $this->templateVars[$construction[3]] = $value;
                $replacedValue .= $this->advancedFilter($construction[4]);
            }
            //unset($this->templateVars[$construction[2]]);
            //unset($this->templateVars[$construction[3]]);
            return $replacedValue;
        } else {
            return $construction[0];
        }
    }

    public function advancedIfDirective($construction)
    {
        $operators = array('==', '===', '!=', '<>', '!==', '>', '<', '>=', '<=', '+', '-', '*', '/', '%');
        $condition = str_replace(' ', '', $construction[1]);
        $usedOperator = false;
        foreach ($operators as $operator) {
            if (strpos($condition, $operator) !== false) {
                $condition = explode($operator, $condition);
                $usedOperator = $operator;
                break;
            }
        }
        if (!$usedOperator || (sizeof($condition) != 2)) {
            return $construction[0];
        }

        foreach ($condition as $key => $cond) {
            if (!is_numeric($cond)) {
                $value = $this->filter("{{var $cond}}");
                $condition[$key] = $value ? $value : 0;
            }
        }
        if (\Magento\Framework\App\ObjectManager::getInstance()
            ->create('\Vnecoms\PdfPro\Model\Math')->compare($condition[0], $condition[1], $usedOperator)) {
            return $construction[2];
        } else {
            return isset($construction[4]) ? $construction[4] : '';
        }
    }

    public function vardumpDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }
        $variable = $this->getVariable($construction[1]) ? $this->getVariable($construction[1]) : null;

        return var_export($variable, true);
    }

    public function incDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }
        $increment = $construction[2];
        if (!is_numeric($increment)) {
            if (!isset($this->templateVars[$increment])) {
                return $construction[0];
            }
            $increment = $this->templateVars[$increment];
        }
        $this->templateVars[$construction[1]] = isset($this->templateVars[$construction[1]]) ? ($this->templateVars[$construction[1]] += $increment) : $increment;
    }

    public function setDirective($construction)
    {
        $variable = isset($construction[1]) ? $construction[1] : false;
        $value = isset($construction[2]) ? $construction[2] : false;
        if (!$variable || !$value) {
            return $construction[0];
        }
        $this->templateVars[$variable] = $value;
    }

    public function barcodeDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }
        $barcodeConfig = $this->helper->getConfig('pdfpro/barcode');
        $fileType = 'PNG'; /* PNG, JPG, GIF */
        $dpi = $barcodeConfig['dpi'];
        $scale = $barcodeConfig['scale'];
        $rotation = $barcodeConfig['rotation'];
        $fontFamily = $barcodeConfig['font_family'];
        $fontSize = (int) $barcodeConfig['font_size'];
        $thickness = $barcodeConfig['thickness'];
        //$checksum = $barcodeConfig['checksum'];
        $code = $barcodeConfig['symbology'];

        $variable = $this->getVariable($construction[1], '') ? $this->getVariable($construction[1], '') : null;
        $variable = urlencode($variable);

        /**
         * @var \CodeItNow\BarcodeBundle\Utils\BarcodeGenerator $barcode
         */
        $barcode = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('CodeItNow\BarcodeBundle\Utils\BarcodeGenerator');
        $barcode->setText($variable);
        $barcode->setType($code);
        $barcode->setScale($scale);
        $barcode->setThickness($thickness);
        $barcode->setFontSize($fontSize);
        $barcode->setLabel($variable);

        return '<img src="data:image/png;base64,'.$barcode->generate().'" />';
    }

    public function qrcodeDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }
        $config = $this->templateVars['config'];
        $qrConfig = $this->helper->getConfig('pdfpro/qrcode');
        $size = $qrConfig['size'];
        $level = $qrConfig['level'];
        $padding = $qrConfig['padding'];
        $fontsize = $qrConfig['font_size'];

        $variable = $this->getVariable($construction[1], '') ? $this->getVariable($construction[1], '') : null;
        $variable = urlencode($variable);


        $qr = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('CodeItNow\BarcodeBundle\Utils\QrCode');

        $qr
            ->setText($variable)
            ->setSize($size)
            ->setPadding($padding)
            ->setErrorCorrection($level)
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel($variable)
            ->setLabelFontSize($fontsize)
        ;

        return '<img src="data:'.$qr->getContentType().';base64,'.$qr->generate().'" />';
    }

    public function getContent($url)
    {
        $agent = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_URL, $url);
        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    public function widgetDirective($construction)
    {
        $variable = isset($construction[1]) ? $construction[1] : false;
        $value = isset($construction[2]) ? $this->helper->processConstruction($construction) : false;

        $type = $this->getVariable('type');                //type of invoice: order , invoice or shipment, creditmemo
        $obj = $this->getVariable($type);                //get data of type

        $totals = $obj->getTotals();                        //get totals body and total footer of invoice

        $column_setting = $value['column'];                    //column settting
        $items = $obj->getItems();                    //items data for invoice
        $type = $value['type'];

        foreach ($column_setting as $_id => $_column) {
            if (!isset($column['option_choose']) and $_column['option_choose'] == null) {
                $_column['option_choose'] = \Vnecoms\PdfPro\Model\Source\Widget\Optiontype::OPTION_TEXT;
            }
            if (!isset($column['option_width']) and $_column['option_width'] == null) {
                $_column['option_width'] = '';
            }
            if (!isset($column['option_height']) and $_column['option_height'] == null) {
                $_column['option_height'] = '';
            }
            if (!isset($column['custom']) and $_column['custom'] == null) {
                $_column['custom'] = '';
            }

            $column_setting[$_id] = $_column;
        }

        /* @var $block \Magento\UrlRewrite\Block\Catalog\Edit\Form */
        $this->widgetBlock->setData(array('object' => $obj, 'value' => $value,
            'type' => $type, 'column' => $column_setting, 'items' => $items,
            'totals' => $totals, ))->setArea('adminhtml')->setIsSecureMode(true)
            ->setTemplate('Vnecoms_PdfPro::filter/widget.phtml');

        return $this->widgetBlock->toHtml();
    }
    
    /**
     * Var directive with modifiers support
     *
     * The |escape modifier is applied by default, use |raw to override
     *
     * @param string[] $construction
     * @return string
     */
    public function varDirective($construction)
    {
        // just return the escaped value if no template vars exist to process
        if (count($this->templateVars) == 0) {
            return $construction[0];
        }
    
        list($directive, $modifiers) = $this->explodeModifiers($construction[2]);
        return $this->applyModifiers($this->getVariable($directive, ''), $modifiers);
    }
}
