<?php

namespace Vnecoms\VendorsCustomRegister\Block\Account\Create;

class Vendor extends \Vnecoms\Vendors\Block\Account\Create\Vendor
{
    /**
     * Get fieldset blocks
     * @return array:
     */
    public function getFieldsetBlocks()
    {
        parent::getFieldsetBlocks();
        foreach($this->_fieldsets as $fieldsetBlock){
            $fieldsetBlock->setTemplate('Vnecoms_VendorsCustomRegister::account/create/fieldset.phtml');
        }

        return $this->_fieldsets;
    }
}
