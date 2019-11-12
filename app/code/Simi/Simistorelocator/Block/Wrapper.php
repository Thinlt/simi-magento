<?php

namespace Simi\Simistorelocator\Block;

class Wrapper extends \Simi\Simistorelocator\Block\AbstractBlock {

    protected $_template = 'Simi_Simistorelocator::wrapper.phtml';

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Simi\Simistorelocator\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout() {
        $this->addChild('simistorelocator.mapbox', 'Simi\Simistorelocator\Block\ListStore\MapBox');
        $this->addChild('simistorelocator.searchbox', 'Simi\Simistorelocator\Block\ListStore\SearchBox');
        $this->addChild('simistorelocator.liststorebox', 'Simi\Simistorelocator\Block\ListStore\ListStoreBox');

        return parent::_prepareLayout();
    }

}
