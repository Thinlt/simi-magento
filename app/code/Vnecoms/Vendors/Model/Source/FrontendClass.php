<?php

namespace Vnecoms\Vendors\Model\Source;

class FrontendClass extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{    
    /**
     * @var array
     */
    protected $_options;
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => '', 'label' => __('None')],
                ['value' => 'validate-number', 'label' => __('Decimal Number')],
                ['value' => 'validate-digits', 'label' => __('Integer Number')],
                ['value' => 'validate-email', 'label' => __('Email')],
                ['value' => 'validate-url', 'label' => __('URL')],
                ['value' => 'validate-alpha', 'label' => __('Letters')],
                ['value' => 'validate-alphanum', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')],
                ['value' => 'no-whitespace', 'label' => __('No Whitespace')],
                ['value' => 'validate-no-html-tags', 'label' => __('No Html Tags')],
                ['value' => 'validate-xml-identifier', 'label' => __('Xml Identifier')],
                ['value' => 'validate-not-negative-number', 'label' => __('Not Negative Number')],
                ['value' => 'validate-code', 'label' => __('Letters (a-z), Numbers (0-9) or Underscore (_)')],
                ['value' => 'validate-identifier', 'label' => __('Identifier')],
            ];
        }
        return $this->_options;
    }
    
    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
