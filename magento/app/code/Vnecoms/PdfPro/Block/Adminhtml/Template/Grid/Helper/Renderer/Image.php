<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Template\Grid\Helper\Renderer;

/**
 * Class Image.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $helper;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array                          $data
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->helper = $helper;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\Object $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $value = $row->getData($this->getColumn()->getIndex());
        if ($value) {
            return '<img alt="{$value}" width="100" src="'.$srcImage.$value.'" />';
        }

        return '<img width="100" src="'.$this->helper->getBaseUrlMedia('ves_pdfpro/templates/default-preview.jpg').'" />';
    }
}
