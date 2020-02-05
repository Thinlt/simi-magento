<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {

    protected function _prepareLayout() {
        \Magento\Framework\Data\Form::setElementRenderer(
                $this->getLayout()->createBlock(
                        'Magento\Backend\Block\Widget\Form\Renderer\Element', $this->getNameInLayout() . '_element'
                )
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
                $this->getLayout()->createBlock(
                        'Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Renderer\Fieldset', $this->getNameInLayout() . '_fieldset'
                )
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
                $this->getLayout()->createBlock(
                        'Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element', $this->getNameInLayout() . '_fieldset_element'
                )
        );
    }

}
