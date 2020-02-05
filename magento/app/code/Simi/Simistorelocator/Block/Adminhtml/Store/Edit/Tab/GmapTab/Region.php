<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab\GmapTab;

class Region extends \Magento\Backend\Block\Template {

    protected $_template = 'Simi_Simistorelocator::store/region.phtml';

    /**
     * @var \Magento\Directory\Helper\Data
     */
    public $directoryHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Directory\Helper\Data          $directoryHelper
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * @return string
     */
    public function getRegionJson() {
        return $this->directoryHelper->getRegionJson();
    }

    /**
     * get registry model.
     *
     * @return \Simi\Simistorelocator\Model\Store
     */
    public function getStore() {
        return $this->getParentBlock()->getRegistryModel();
    }
}
