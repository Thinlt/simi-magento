<?php

namespace Simi\Simistorelocator\Model;


interface StoreUrlPathGeneratorInterface
{
    /**
     * @param \Simi\Simistorelocator\Model\Store $store
     *
     * @return string
     */
    public function getUrlPath(\Simi\Simistorelocator\Model\Store $store);

    /**
     * Get canonical store url path.
     *
     * @param \Simi\Simistorelocator\Model\Store $store
     *
     * @return string
     */
    public function getCanonicalUrlPath(\Simi\Simistorelocator\Model\Store $store);

    /**
     * Generate store view page url key based on rewrite_request_path entered by merchant or store name
     *
     * @param \Simi\Simistorelocator\Model\Store $store
     * @return string
     * @api
     */
    public function generateUrlKey(\Simi\Simistorelocator\Model\Store $store);
}