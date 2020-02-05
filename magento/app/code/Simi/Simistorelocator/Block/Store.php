<?php

namespace Simi\Simistorelocator\Block;

class Store extends \Simi\Simistorelocator\Block\AbstractBlock {

    /**
     * Store constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        \Simi\Simistorelocator\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

}
