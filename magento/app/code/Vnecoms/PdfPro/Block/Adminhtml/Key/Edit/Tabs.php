<?php

namespace VnEcoms\PdfPro\Block\Adminhtml\Key\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * @method Tabs setTitle(\string $title)
 */
class Tabs extends WidgetTabs
{
    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('key_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('API Key Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_eventManager->dispatch('ves_pdfpro_apikey_tabs_before', ['block' => $this]);
        $this->addTab(
            'form_section',
            [
                'label' => __('Information'),
                'title' => __('Information'),
                'content' => $this->getLayout()
                    ->createBlock('VnEcoms\PdfPro\Block\Adminhtml\Key\Edit\Tab\Form')
                    ->toHtml(),
            ]
        );
        $this->_eventManager->dispatch('ves_pdfpro_apikey_tabs_after', ['block' => $this]);

        return parent::_beforeToHtml();
    }
}
