<?php

namespace Vnecoms\VendorsShippingFlatRate\Block\Adminhtml\System\Config\Form\Field;

class Rates extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $block = $this->getLayout()->createBlock('Vnecoms\VendorsShippingFlatRate\Block\Adminhtml\System\Config\Form\Field\Rates\Field');
        $block->setElement($element);

        return $block->toHtml();
    }
}
