<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Helper;

use Magento\Framework\Data\Form\Element\Editor as EditorField;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use Vnecoms\PdfPro\Model\TemplateFactory as TemplateFactory;
use Magento\Framework\Registry;
use Magento\Framework\Escaper;

/**
 * Class Editor.
 */
class Editor extends EditorField
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    public function __construct(
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        Registry $registry,
        TemplateFactory $templateFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\View\Asset\Repository $repository,
        \Vnecoms\PdfPro\Helper\Data $helper,
        $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_coreRegistry = $registry;
        $this->templateFactory = $templateFactory;
        $this->_backendUrl = $backendUrl;
        $this->_assetRepo = $repository;
        $this->helper = $helper;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @return string
     */
    public function getConfig($key = null)
    {
        $config = $this->_wysiwygConfig->getConfig(['tab_id' => 'form_section']);

        /*Attach CSS file from selected template*/
        $config['content_css'] = $this->helper->getBaseUrlMedia('ves_pdfpro/templates/default.css');
        if ($apiKey = $this->_coreRegistry->registry('current_key')) {
            if ($apiKey->getId()) {
                $template = $this->templateFactory->create()->load($apiKey->getTemplateId());
                $config['content_css'] .= ','.$this->helper->getBaseUrlMedia($template->getData('css_path'));
                /*Body class*/
                $config['body_class'] = $template->getSku();
            }
        }
        $config['widget_window_url'] = $this->_backendUrl->getUrl('vnecoms_pdfpro/widget/index');

        $config['ves_widget_images_url'] = $this->helper->getCustomPlaceholderImagesBaseUrl();
        $config['ves_widget_placeholders'] = $this->helper->getCustomAvailablePlaceholderFilenames();
        $config['custom_image_filename'] = $this->helper->getCustomImageFileName();

        //fix directives_url and directives_url_quoted
        $plugins = $config->getData('plugins');
        $plugins[0]['options']['url'] = $this->_backendUrl->getUrl('vnecoms_pdfpro/system_variable/wysiwygPlugin');
        $plugins[0]['options']['onclick']['subject'] = "MagentovariablePlugin.loadChooser('{{html_id}}');";

        $config['plugins'] = $plugins;

        if ($key) {
            return $config->getData($key);
        }

        return $config;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getElementHtml()
    {
        $js = '
            <script type="text/javascript">
            //<![CDATA[
                var vesEditorConfigJSON = '.json_encode($this->getConfig()->getData()).';
                openEditorPopup = function(url, name, specs, parent) {
                    if ((typeof popups == "undefined") || popups[name] == undefined || popups[name].closed) {
                        if (typeof popups == "undefined") {
                            popups = new Array();
                        }
                        var opener = (parent != undefined ? parent : window);
                        popups[name] = opener.open(url, name, specs);
                    } else {
                        popups[name].focus();
                    }
                    return popups[name];
                }

                closeEditorPopup = function(name) {
                    if ((typeof popups != "undefined") && popups[name] != undefined && !popups[name].closed) {
                        popups[name].close();
                    }
                }
            //]]>
            </script>';

        if ($this->isEnabled()) {
            // add Firebug notice translations
            $warn = 'Firebug is known to make the WYSIWYG editor slow unless it is turned off or configured properly.';
            $this->getConfig()->addData(array(
                'firebug_warning_title' => $this->translate('Warning'),
                'firebug_warning_text' => $this->translate($warn),
                'firebug_warning_anchor' => $this->translate('Hide'),
            ));
            $translatedString = array(
                'Insert Image...' => $this->translate('Insert Image...'),
                'Insert Media...' => $this->translate('Insert Media...'),
                'Insert File...' => $this->translate('Insert File...'),
            );

            $jsSetupObject = 'wysiwyg'.$this->getHtmlId();

            $forceLoad = '';
            if (!$this->isHidden()) {
                if ($this->getForceLoad()) {
                    $forceLoad = $jsSetupObject.'.setup("exact");';
                } else {
                    $forceLoad = 'Event.observe(window, "load", '.
                        $jsSetupObject.
                        '.setup.bind('.
                        $jsSetupObject.
                        ', "exact"));';
                }
            }

            $html = $this->_getButtonsHtml().
                '<textarea name="'.
                $this->getName().
                '" title="'.
                $this->getTitle().
                '" '.
                $this->_getUiId().
                ' id="'.
                $this->getHtmlId().
                '"'.
                ' class="textarea'.
                $this->getClass().
                '" '.
                $this->serialize(
                    $this->getHtmlAttributes()
                ).
                ' >'.
                $this->getEscapedValue().
                '</textarea>'.
                $js.
                '
                <script type="text/javascript">
                //<![CDATA[
                window.tinyMCE_GZ = window.tinyMCE_GZ || {}; window.tinyMCE_GZ.loaded = true;require(["jquery", "mage/translate", "mage/adminhtml/events", "mage/adminhtml/wysiwyg/tiny_mce/setup", "mage/adminhtml/wysiwyg/widget"], function(jQuery){' .
                "\n" .
                '  (function($) {$.mage.translate.add(' .
                \Zend_Json::encode(
                    $this->getButtonTranslations()
                ) .
                ')})(jQuery);' .
                "\n" .

                $jsSetupObject.
                ' = new tinyMceWysiwygSetup("'.
                $this->getHtmlId().
                '", '.
                'vesEditorConfigJSON'.
                ');'.
                'window.'.$jsSetupObject.' = '.$jsSetupObject.';'
                .$jsSetupObject.'.setBodyClass(vesEditorConfigJSON.body_class);'.
                $forceLoad.
                '
                    editorFormValidationHandler = '.
                $jsSetupObject.
                '.onFormValidation.bind('.
                $jsSetupObject.
                ');
                    Event.observe("toggle'.
                $this->getHtmlId().
                '", "click", '.
                $jsSetupObject.
                '.toggle.bind('.
                $jsSetupObject.
                '));

                    varienGlobalEvents.attachEventHandler("formSubmit", editorFormValidationHandler);
                    varienGlobalEvents.attachEventHandler("tinymceBeforeSetContent", '
                .$jsSetupObject.'.beforeSetContent.bind('.$jsSetupObject.'));
                    varienGlobalEvents.attachEventHandler("tinymceSaveContent", '
                .$jsSetupObject.'.saveContent.bind('.$jsSetupObject.'));
                    varienGlobalEvents.clearEventHandlers("open_browser_callback");
                    varienGlobalEvents.attachEventHandler("open_browser_callback", '.
                $jsSetupObject.
                '.openFileBrowser);
                //]]>
                });
                </script>';

            $html = $this->_wrapIntoContainer($html);
            $html .= $this->getAfterElementHtml();

            return $html;
        } else {
            // Display only buttons to additional features
            if ($this->getConfig('widget_window_url')) {
                $html = $this->_getButtonsHtml().$js.parent::getElementHtml();
                if ($this->getConfig('add_widgets')) {
                    $html .= '<script type="text/javascript">
                    //<![CDATA[
                    require(["jquery", "mage/translate", "mage/adminhtml/wysiwyg/widget"], function(jQuery){
                        (function($) {
                            $.mage.translate.add('.\Zend_Json::encode($this->getButtonTranslations()).')
                        })(jQuery);
                    });
                    //]]>
                    </script>';
                }
                $html = $this->_wrapIntoContainer($html);

                return $html;
            }

            return parent::getElementHtml();
        }
    }

    /**
     * Return HTML button to preview WYSIWYG.
     *
     * @param bool $visible
     *
     * @return string
     */
    protected function _getPreviewButtonHtml($visible = true)
    {
        $html = $this->_getButtonHtml(
            [
                'title' => $this->translate('Preview'),
                'class' => 'preview',
                'style' => $visible ? '' : 'display:none',
                'id' => 'preview'.$this->getHtmlId(),
                'onclick' => "require(['prototype','Vnecoms_PdfPro::jquery-fancybox/jquery.mousewheel-3.0.4.pack', 'Vnecoms_PdfPro::jquery-fancybox/jquery.fancybox-1.3.4.pack', 'tinymce'], function(){
                    console.log(tinyMCE.get('".$this->getHtmlId()."').getContent());
                    $('#preview".$this->getHtmlId()."').fancybox({
                        'width'				: '75%',
                        'height'			: '75%',
                        'autoScale'			: false,
                        'transitionIn'		: 'none',
                        'transitionOut'		: 'none',
                        'href'              : 'http://facebook.com/',
                    });
                });",

            ]
        );

        return $html;
    }

    /**
     * Return Editor top Buttons HTML
     * add preview buttons html.
     *
     * @return string
     */
    protected function _getButtonsHtml()
    {
        $buttonsHtml = '<div id="buttons'.$this->getHtmlId().'" class="buttons-set">';
        if ($this->isEnabled()) {
            $buttonsHtml .= $this->_getToggleButtonHtml().$this->_getPluginButtonsHtml($this->isHidden()).'';//$this->_getPreviewButtonHtml(true);
        } else {
            $buttonsHtml .= $this->_getPluginButtonsHtml(true).'';// $this->_getPreviewButtonHtml(true);
        }
        $buttonsHtml .= '</div>';

        return $buttonsHtml;
    }
}
