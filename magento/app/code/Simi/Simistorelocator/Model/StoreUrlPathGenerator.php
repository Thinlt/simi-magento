<?php

namespace Simi\Simistorelocator\Model;

use Magento\Cms\Api\Data\PageInterface;

class StoreUrlPathGenerator implements StoreUrlPathGeneratorInterface {

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    public $filterManager;

    /**
     * StoreUrlPathGenerator constructor.
     *
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     */
    public function __construct(
    \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        $this->filterManager = $filterManager;
    }

    /**
     * @param \Simi\Simistorelocator\Model\Store $store
     *
     * @return string
     */
    public function getUrlPath(\Simi\Simistorelocator\Model\Store $store) {
        $urlKey = $store->getRewriteRequestPath();

        return $urlKey === '' || $urlKey === null ? $store->getStoreName() : $urlKey;
    }

    /**
     * Get canonical store url path.
     *
     * @param \Simi\Simistorelocator\Model\Store $store
     *
     * @return string
     */
    public function getCanonicalUrlPath(\Simi\Simistorelocator\Model\Store $store) {
        return 'simistorelocator/index/view/storelocator_id/' . $store->getId();
    }

    /**
     * Generate store view page url key based on rewrite_request_path entered by merchant or store name.
     *
     * @param PageInterface $store
     * @return string
     * @api
     */
    public function generateUrlKey(\Simi\Simistorelocator\Model\Store $store) {
        return $this->filterManager->translitUrl(
                        $this->getUrlPath($store)
        );
    }

}
