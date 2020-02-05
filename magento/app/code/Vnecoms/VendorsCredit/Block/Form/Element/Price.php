<?php
namespace Vnecoms\VendorsCredit\Block\Form\Element;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Price extends AbstractElement
{
    /**
     * Get Element HTML
     * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
     */
    public function getElementHtml()
    {
        return '<span class="withdrawal-price">'.$this->getValue().'</span>';
    }
}
