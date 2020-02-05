<?php
namespace Vnecoms\Vendors\Block\Widget\Grid\Column\Renderer;

class Html extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return mixed
     */
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        $defaultValue = $this->getColumn()->getDefault();
        $data = parent::_getValue($row);
        $string = $data === null ? $defaultValue : $data;
        return $string;
    }
}
