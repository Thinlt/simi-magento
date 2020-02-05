<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\PdfProCustomVariables\Block\Adminhtml\Grid\Renderer;

/**
 * custom variables grid action column renderer
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->getColumn()->setActions(
            [
                [
                    'url' => $this->getUrl('ves_customvariable/variables/edit', ['custom_variable_id' => $row->getCustomVariableId()]),
                    'caption' => __('Edit'),
                ],
            ]
        );
        return parent::render($row);
    }
}
