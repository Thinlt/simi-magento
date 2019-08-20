<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Key\Helper\Renderer;

class Store extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $arrVal = explode(',', $value);
        if (sizeof($arrVal) == 1) {
            $array = $value;
        } else {
            $array = $arrVal;
        }
        $row->setData($this->getColumn()->getIndex(), $array);

        return parent::render($row);
    }
}
