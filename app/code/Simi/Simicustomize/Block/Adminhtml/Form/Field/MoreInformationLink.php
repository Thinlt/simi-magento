<?php

namespace Simi\Simicustomize\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class MoreInformationLink extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('information_title', ['label' => __('Information Title'), 'class' => 'required-entry', 'size' => '200px']);
        $this->addColumn('information_link', ['label' => __('Link'), 'class' => 'required-entry', 'size' => '400px']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Information');
    }
}
