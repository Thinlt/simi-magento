<?php
namespace Vnecoms\VendorsLanguage\Model\Source;

class Language extends \Magento\Config\Model\Config\Source\Locale
{
    /**
     * (non-PHPdoc)
     * @see \Magento\Config\Model\Config\Source\Locale::toOptionArray()
     */
    public function toOptionArray(){
        $options = parent::toOptionArray();
        return $options;
    }
}
