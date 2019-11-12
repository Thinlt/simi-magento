<?php

namespace Simi\Simistorelocator\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Simi\Simistorelocator\Model\Config\Source\OrderTypeStore;

class Loadstore extends \Simi\Simistorelocator\Controller\Index {

    /**
     * Default current page.
     */
    const DEFAULT_CURRENT_PAGINATION = 1;

    /**
     * Default range pagination.
     */
    const DEFAULT_RANGE_PAGINATION = 5;

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute() {
        /** @var \Simi\Simistorelocator\Model\ResourceModel\Store\Collection $collection */
        $collection = $this->_filterStoreCollection($this->storeCollectionFactory->create());

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $pager = $resultPage->getLayout()->createBlock(
                'Simi\Simistorelocator\Block\ListStore\Pagination', 'simistorelocator.pager', [
            'collection' => $collection,
            'data' => ['range' => self::DEFAULT_RANGE_PAGINATION],
                ]
        );

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');

        $response->setContents(
                $this->jsonHelper->jsonEncode(
                        [
                            'storesjson' => $collection->prepareJson(),
                            'pagination' => $pager->toHtml(),
                            'num_store' => $collection->getSize(),
                        ]
                )
        );

        return $response;
    }

    /**
     * filter store.
     *
     * @param \Simi\Simistorelocator\Model\ResourceModel\Store\Collection $collection
     *
     * @return \Simi\Simistorelocator\Model\ResourceModel\Store\Collection
     */
    protected function _filterStoreCollection(
        \Simi\Simistorelocator\Model\ResourceModel\Store\Collection $collection
    ) {
        $collection->addFieldToSelect([
            'store_name',
            'phone',
            'address',
            'latitude',
            'longitude',
            'marker_icon',
            'zoom_level',
            'rewrite_request_path',
        ]);

        $curPage = $this->getRequest()->getParam('curPage', self::DEFAULT_CURRENT_PAGINATION);
        $collection->setPageSize($this->systemConfig->getPainationSize())->setCurPage($curPage);

        /*
         * Filter store enabled
         */
        $collection->addFieldToFilter('status', \Simi\Simistorelocator\Model\Status::STATUS_ENABLED);

        /*
         * filter by radius
         */
        if ($radius = $this->getRequest()->getParam('radius')) {
            $latitude = $this->getRequest()->getParam('latitude');
            $longitude = $this->getRequest()->getParam('longitude');
            $collection->addLatLngToFilterDistance($latitude, $longitude, $radius);
        }

        /*
         * filter by tags
         */
        $tagIds = $this->getRequest()->getParam('tagIds');
        if (!empty($tagIds)) {
            $collection->addTagsToFilter($tagIds);
        }

        /*
         * filter by store information
         */

        if ($countryId = $this->getRequest()->getParam('countryId')) {
            $collection->addFieldToFilter('country_id', $countryId);
        }

        if ($storeName = $this->getRequest()->getParam('storeName')) {
            $collection->addFieldToFilter('store_name', ['like' => "%$storeName%"]);
        }

        if ($state = $this->getRequest()->getParam('state')) {
            $collection->addFieldToFilter('state', ['like' => "%$state%"]);
        }

        if ($city = $this->getRequest()->getParam('city')) {
            $collection->addFieldToFilter('city', ['like' => "%$city%"]);
        }

        if ($zipcode = $this->getRequest()->getParam('zipcode')) {
            $collection->addFieldToFilter('zipcode', ['like' => "%$zipcode%"]);
        }

        // Set sort type for list store
        switch ($this->systemConfig->getSortStoreType()) {
            case OrderTypeStore::SORT_BY_ALPHABETICAL:
                $collection->setOrder('store_name', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);
                break;

            case OrderTypeStore::SORT_BY_DISTANCE:
                if ($radius) {
                    $collection->setOrder('distance', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);
                }
                break;
            default:
                $collection->setOrder('sort_order', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);
        }

        // Allow load base image for each store
        $collection->setLoadBaseImage(true);

        return $collection;
    }

}
