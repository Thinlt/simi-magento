<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Template\Helper;

class Preview extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    public function getElementHtml()
    {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Vnecoms\PdfPro\Helper\Data');

        if ($this->getEscapedValue()) {
            $url = $helper->getBaseUrlMedia($this->getEscapedValue());
        } else {
            $url = $helper->getBaseUrlMedia('ves_pdfpro/templates/default-preview.jpg');
        }

        return '<img width="400px;" id="'.$this->getHtmlId().'" src="'.$url.'" alt="'.$this->getHtmlId().'" />';
    }
}
