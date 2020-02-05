<?php
 
// namespace Simi\VendorMapping\Block\Vendors;
 
// use Magento\Backend\Block\Template\Context;
// use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
 
// class WysiwygEditor extends \Magento\Config\Block\System\Config\Form\Field
// {
//     public function __construct(
//         Context $context,
//         WysiwygConfig $wysiwygConfig,
//         array $data = []
//     )
//     {
//         $this->_wysiwygConfig = $wysiwygConfig;
//         parent::__construct($context, $data);
//     }
 
//     protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
//     {
//         $element->setWysiwyg(true);
//         $element->setConfig($this->_wysiwygConfig->getConfig($element));
//         return parent::_getElementHtml($element);
//     }
// }