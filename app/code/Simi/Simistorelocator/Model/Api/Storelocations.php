<?php

/**
 * Created by PhpStorm.
 * User: scottsimicart
 * Date: 10/2/17
 * Time: 3:04 PM
 */

namespace Simi\Simistorelocator\Model\Api;

use Simi\Simiconnector\Model\Api\Apiabstract as Api;

class Storelocations extends Api
{
    public $DEFAULT_ORDER = 'store_name';

    public $ylat;
    public $ylng;

    public $simiconnectorHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Simi\Simiconnector\Helper\Data $helper
    )
    {
        parent::__construct($simiObjectManager);
        $this->simiconnectorHelper = $helper;
    }

    public function setBuilderQuery()
    {
        $data = $this->getData();
        $this->ylat = $data['params']['lat'];
        $this->ylng = $data['params']['lng'];

        if ($data['resourceid']) {
            $this->builderQuery = $this->simiObjectManager->create('\Simi\Simistorelocator\Model\Store')
                ->load($data['resourceid']);
        } else {
            $this->builderQuery = $this->getStoreList();
        }
    }

    public function getStoreList()
    {
        $data = $this->getData();
        $typeID = $this->simiconnectorHelper->getVisibilityTypeId('storelocator');
        $visibilityTable = $visibilityTable = $this->resource->getTableName('simiconnector_visibility');
        $storelocatorCollections = $this->simiObjectManager
            ->create('\Simi\Simistorelocator\Model\Store')
            ->getCollection()
            ->addFieldToFilter('status', 1)
            ->applyAPICollectionFilter($visibilityTable, $typeID, $this->storeManager
                ->getStore()->getId());
        $this->searchArea($data, $storelocatorCollections);

        $tagsIds = [];
        if (isset($data['params']['tag'])) {
            $tagsIds = $this->getStoreToTag($data['params']['tag']);
        }
        if (count($tagsIds))
            $storelocatorCollections->addTagsToFilter($tagsIds);
        return $storelocatorCollections;
    }

    public function searchArea($data, $collection)
    {
        $data = (object)$data['params'];
        if (isset($data->country) && $data->country && $data->country != "") {
            $collection->addFieldToFilter('country_id', array('like' => '%' . $data->country . '%'));
        }
        if (isset($data->city) && ($data->city != null) && $data->city != "") {
            $city = trim($data->city);
            $collection->addFieldToFilter('city', array('like' => '%' . $city . '%'));
        }
        if (isset($data->state) && ($data->state != null) && $data->state != "") {
            $state = trim($data->state);
            $collection->addFieldToFilter('state', array('like' => '%' . $state . '%'));
        }
        if (isset($data->zipcode) && ($data->zipcode != null) && $data->zipcode != "") {
            $zipcode = trim($data->zipcode);
            $collection->addFieldToFilter('zipcode', array('like' => '%' . $zipcode . '%'));
        }
        return $collection;
    }

    public function index()
    {
        $storeArray = array();
        if ($this->ylng != 0 && $this->ylat != 0) {
            foreach ($this->getStoreList() as $item) {
                $latitude = $item->getLatitude();
                $longtitude = $item->getLongtitude();
                $distance = $this->calculationByDistance($this->ylat, $this->ylng, $latitude, $longtitude);
                $storeArray[(string)$item->getId()] = $distance;
            }
            asort($storeArray);
            $this->builderQuery->getSelect()->order(new \Zend_Db_Expr('FIELD(simistorelocator_id, "' . implode('","', array_keys($storeArray)) . '") ASC'));
        }
        $result = parent::index();
        foreach ($result['storelocations'] as $index => $storeReturn) {
            $distance = 0;
            $item = $this->simiObjectManager->create('\Simi\Simistorelocator\Model\Store')
                ->load($storeReturn['simistorelocator_id']);
            $storeReturn = $item->toArray();
            $latitude = $item->getLatitude();
            $longtitude = $item->getLongtitude();

            if ($this->ylng != 0 && $this->ylat != 0) {
                $distance = $this->calculationByDistance($this->ylat, $this->ylng, $latitude, $longtitude);
            }
            $storeReturn["special_days"] = $item->getSpecialdaysDataApp();
            $storeReturn["holiday_days"] = $item->getHolidaysDataApp();
            $storeReturn["country_name"] = $item->getCountryName();
            $storeReturn["distance"] = $distance;
//           Return first image in the list images as base image if not select base image
            if (isset($storeReturn["baseimage"])) {
                $storeReturn["image"] = $this->storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . $storeReturn['baseimage'];
            } else {
//           Return default store image as base image if user not config.
                $storeReturn["image"] = "http://localhost:81/cms/pub/static/version1557471173/adminhtml/Magento/backend/en_US/Simi_Simistorelocator/images/default_store.png";
            }
            $result['storelocations'][$index] = $storeReturn;
        }
        return $result;
    }

    public function calculationByDistance($mlat, $mlng, $lat, $lng)
    {
        $latFrom = deg2rad($mlat);
        $lonFrom = deg2rad($mlng);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);
        $earthRadius = 6371000;
        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }

    public function getStoreToTag($value)
    {
        $tagIds = [];
        $tagCollection = $this->simiObjectManager
            ->create('\Simi\Simistorelocator\Model\Tag')
            ->getCollection()
            ->addFieldToFilter('tag_name', array('eq' => $value));

        foreach ($tagCollection as $item) {
            if (!in_array($item->getData("tag_id"), $tagIds)) {
                $tagIds[] = $item->getData("tag_id");
            }
        }
        return $tagIds;
    }
}
