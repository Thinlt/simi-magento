<?php

namespace Simi\Simistorelocator\Block\ListStore;

class ListStoreBox extends \Simi\Simistorelocator\Block\AbstractBlock {

    protected $_template = 'Simi_Simistorelocator::liststore/liststorebox.phtml';

    /**
     * Block constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Simi\Simistorelocator\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
