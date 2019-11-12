<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Framework\UrlInterface;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Context             $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array                                      $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * @var array
     */
    public $values;

    /**
     * Renders grid column.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if ($value = $row->getData($this->getColumn()->getIndex())) {
            $srcImage = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $value;

            return '<img style="display: block;margin: auto;" width="120" height="60" src="' . $srcImage . '" />';
        } else {
            return '';
        }
    }
}
