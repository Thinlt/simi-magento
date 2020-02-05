<?php
namespace Vnecoms\Vendors\Block\System\Config\Form\Field;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    
    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }
    
    
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $element->setTheme('simple');
        $element->setWysiwyg(true);
        $element->setConfig($wysiwygConfig);
        return parent::_getElementHtml($element);
    }
}
