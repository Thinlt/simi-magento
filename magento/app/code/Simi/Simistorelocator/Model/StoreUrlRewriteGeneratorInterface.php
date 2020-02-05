<?php


namespace Simi\Simistorelocator\Model;


interface StoreUrlRewriteGeneratorInterface
{
    /**
     * @param \Simi\Simistorelocator\Model\Store $store
     *
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate(\Simi\Simistorelocator\Model\Store $store);
}