<?php

namespace Simi\Simicustomize\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Pwatitles extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('url_path', ['label' => __('URL path'), 'class' => 'required-entry', 'size' => '200px' ]);
        $this->addColumn('meta_title', ['label' => __('Meta Title'), 'class' => 'required-entry', 'size' => '400px']);
        $this->addColumn('meta_description', ['label' => __('Meta Description'), 'class' => 'required-entry', 'size' => '400px']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
