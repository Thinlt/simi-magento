<?php

namespace Simi\Simicustomize\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class CustomerServiceLink extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('service_title', ['label' => __('Service Title'), 'class' => 'required-entry', 'size' => '200px']);
        $this->addColumn('service_link', ['label' => __('Link'), 'class' => 'required-entry', 'size' => '400px']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Service');
    }
}
