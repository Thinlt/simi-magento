<?php

namespace Vnecoms\PdfPro\Model;

use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

/**
 * Widget model for different purposes.
 */
class Widget extends \Magento\Widget\Model\Widget
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;

    /**
     * @var WysiwygConfig
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Framework\Escaper               $escaper
     * @param \Magento\Widget\Model\Config\Data        $dataStorage
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\Asset\Source     $assetSource
     * @param \Magento\Framework\View\FileSystem       $viewFileSystem
     * @param \Magento\Widget\Helper\Conditions        $conditionsHelper
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Widget\Model\Config\Data $dataStorage,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\Asset\Source $assetSource,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Vnecoms\PdfPro\Helper\Data $helper,
        WysiwygConfig $wysiwygConfig
    ) {
        $this->_helper = $helper;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($escaper, $dataStorage, $assetRepo, $assetSource, $viewFileSystem, $conditionsHelper);
    }

    /**
     * Return widget presentation code in WYSIWYG editor.
     *
     * @param string $type   Widget Type
     * @param array  $params Pre-configured Widget Params
     * @param bool   $asIs   Return result as widget directive(true) or as placeholder image(false)
     *
     * @return string Widget directive ready to parse
     *
     * @api
     */
    public function getWidgetDeclaration($type, $params = [], $asIs = true)
    {
        $directive = '{{widget type="'.$type.'"';

        foreach ($params as $name => $value) {
            // Retrieve default option value if pre-configured
            if ($name == 'conditions') {
                $name = 'conditions_encoded';
                $value = $this->conditionsHelper->encode($value);
            } elseif (is_array($value)) {
                $value = base64_encode(serialize($value));
            } elseif (trim($value) == '') {
                $widget = $this->getConfigAsObject($type);
                $parameters = $widget->getParameters();
                if (isset($parameters[$name]) && is_object($parameters[$name])) {
                    $value = $parameters[$name]->getValue();
                }
            }
            if ($value) {
                $directive .= sprintf(' %s="%s"', $name, $value);
            }
        }
        $directive .= '}}';

        if ($asIs) {
            return $directive;
        }

        $html = sprintf(
            '<div class="widget"><img id="%s" src="%s" title="%s"></div>',
            $this->idEncode($directive),
            $this->_helper->getPlaceholderImageUrl(),
            $this->escaper->escapeUrl($directive)
        );

        return $html;
    }
}
