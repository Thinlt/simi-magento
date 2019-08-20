<?php

namespace Vnecoms\VendorsConfig\Block\System\Config\Form\Fieldset;

class Factory extends \Magento\Config\Block\System\Config\Form\Fieldset\Factory
{
    /**
     * (non-PHPdoc)
     * @see \Magento\Config\Block\System\Config\Form\Fieldset\Factory::create()
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create('Vnecoms\VendorsConfig\Block\System\Config\Form\Fieldset', $data);
    }
}
