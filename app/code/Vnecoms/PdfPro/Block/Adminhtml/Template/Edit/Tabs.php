<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Template\Edit;

/**
 * Class Tabs.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('template_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Theme Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_section',
            [
                'label' => __('Theme Information'),
                'title' => __('Theme Information'),
                'content' => $this->getLayout()
                        ->createBlock('Vnecoms\PdfPro\Block\Adminhtml\Template\Edit\Tab\Form')
                        ->toHtml(),
            ]
        );

        return parent::_beforeToHtml();
    }
}
