<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 12/01/2017
 * Time: 22:41
 */

namespace Vnecoms\PdfPro\Plugin\Model\Variable;


class Config
{

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;

    /** @var  \Magento\Framework\DataObject */
    protected  $config;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Backend\Model\UrlInterface $url
    )
    {
        $this->_assetRepo = $assetRepo;
        $this->_url = $url;
    }

    /**
     * @param \Magento\Variable\Model\Variable\Config $subject
     * @param \Magento\Framework\DataObject $config
     * @param \Closure $proceed
     * @param array $result
     * @return array
     */
    public function afterGetWysiwygPluginSettings(
        \Magento\Variable\Model\Variable\Config $subject
    )
    {
        $variableConfig = [];
        $onclickParts = [
            'search' => ['html_id'],
            'subject' => 'EasyPdfvariablePlugin.loadChooser(\'' .
                $this->getVariablesWysiwygActionUrl() .
                '\', \'{{html_id}}\');',
        ];
        $variableWysiwyg = [
            [
                'name' => 'magentovariable',
                'src' => $this->getWysiwygJsPluginSrc(),
                'options' => [
                    'title' => __('Insert Variable 123123...'),
                    'url' => $this->getVariablesWysiwygActionUrl(),
                    'onclick' => $onclickParts,
                    'class' => 'add-variable customPDFVariable plugin',
                ],
            ],
        ];

        //$configPlugins = $subject->getData('plugins');
        $variableConfig['plugins'] = array_merge($variableWysiwyg);
        //$result = $proceed($variableWysiwyg);
        return $variableConfig;
    }

    /**
     * Return url to wysiwyg plugin
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getWysiwygJsPluginSrc()
    {
        $editorPluginJs = 'Vnecoms_PdfPro/js/pdfvariable/editor_plugin.js';
        return $this->_assetRepo->getUrl($editorPluginJs);
    }

    /**
     * Return url of action to get variables
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getVariablesWysiwygActionUrl()
    {
        return $this->_url->getUrl('vnecoms_pdfpro/system_variable/wysiwygPlugin');
    }
}
