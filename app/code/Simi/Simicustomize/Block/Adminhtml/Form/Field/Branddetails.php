<?php

namespace Simi\Simicustomize\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Branddetails extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('brand_title', ['label' => __('Brand Title (By Store Locale)'), 'class' => 'required-entry', 'size' => '200px' ]);
        $this->addColumn('brand_description', ['label' => __('Brand description'), 'class' => 'required-entry', 'size' => '400px']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
