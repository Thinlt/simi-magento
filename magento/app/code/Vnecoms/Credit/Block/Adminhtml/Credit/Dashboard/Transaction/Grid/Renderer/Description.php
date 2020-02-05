<?php

namespace Vnecoms\Credit\Block\Adminhtml\Credit\Dashboard\Transaction\Grid\Renderer;


class Description extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        return parent::_getValue($row);
    }
}
