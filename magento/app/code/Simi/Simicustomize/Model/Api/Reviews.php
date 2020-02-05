<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simicustomize\Model\Api;

class Reviews extends \Simi\Simiconnector\Model\Api\Apiabstract
{

    public $helper;
    public $allow_filter_core = false;

    public function setBuilderQuery()
    {
        $this->helper = $this->simiObjectManager->get('\Simi\Simiconnector\Helper\Review');
        $data          = $this->getData();
        $parameters    = $data['params'];
        if ($data['resourceid']) {
            $this->builderQuery = $this->helper->getReview($data['resourceid']);
        } else {
            if (isset($parameters[self::FILTER])) {
                $filter             = $parameters[self::FILTER];
                $this->builderQuery = $this->helper->getReviews($filter['product_id']);
            }
        }
    }

    /**
     * @return collection
     * override
     */
    public function filter()
    {
        $data       = $this->data;
        $parameters = $data['params'];
        if ($this->allow_filter_core) {
            $query = $this->builderQuery;
            $this->_whereFilter($query, $parameters);
        }
        if (isset($parameters['dir']) && isset($parameters['order'])) {
            $this->_order($parameters);
        }

        return null;
    }

    /**
     * @return override
     */
    public function store()
    {
        $data       = $this->getData();
        $content    = $data['contents_array'];
        // add vendor_id from product to review table
        $content['vendor_id'] = null;
        if (isset($content['product_id'])) {
            $product = $this->helper->_initProduct($content['product_id']);
            if($product && $product->getId() && $product->getVendorId()){
                if ($product->getVendorId() == 'default'){
                    $content['vendor_id'] = null;
                } else {
                    $content['vendor_id'] = $product->getVendorId();
                }
            }
        }
        $review     = $this->helper->saveReview($content);
        $entity     = $review['review'];
        $parameters = $data['params'];
        $fields     = [];
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info              = $entity->toArray($fields);
        $detail            = $this->getDetail($info);
        $detail['message'] = $review['message'];
        return $detail;
    }

    /**
     * @param $info
     * @param $all_ids
     * @param $total
     * @param $page_size
     * @param $from
     * @return array
     * override
     */
    public function getListReview($info, $all_ids, $total, $page_size, $from, $count)
    {
        return [
            'all_ids'             => $all_ids,
            $this->getPluralKey() => $info,
            'total'               => $total,
            'page_size'           => $page_size,
            'from'                => $from,
            'count'               => $count,
        ];
    }

    /**
     * @return array
     * @throws Exception
     * override
     */
    public function index()
    {
        $collection = $this->builderQuery;
        $this->filter();
        $data       = $this->getData();
        $parameters = $data['params'];
        $page       = 1;
        $limit = self::DEFAULT_LIMIT;
        $offset = 0;
        $this->setPageSize($collection, $parameters, $limit, $offset, $page);
        $all_ids = [];
        $info    = [];
        $total   = $collection->getSize();
        if ($offset > $total) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Review is empty.'), 4);
        }
        
        $fields = [];
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        // $star    = [];
        $count   = null;
        // $star[0] = 0;
        // $star[1] = 0;
        // $star[2] = 0;
        // $star[3] = 0;
        // $star[4] = 0;
        // $star[5] = 0;

        $check_limit  = 0;
        $check_offset = 0;
        foreach ($collection as $entity) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit) {
                break;
            }
            // $star[5] ++;
            $y = 0;
            foreach ($entity->getRatingVotes() as $vote) {
                $y += ($vote->getPercent() / 20);
            }
            $count = $this->simiObjectManager
            ->get('Simi\Simiconnector\Helper\Data')->countArray($entity->getRatingVotes());
            $count = $count == 0 ? 1 : $count;
            $x                          = (int) ($y / $count);
            $info_detail                = $entity->toArray($fields);
            $info_detail['rate_points'] = $x;
            $info[]                     = $info_detail;
            // $z = $y % 3;
            // $x = $z < 5 ? $x : $x + 1;
            // $this->applyStarCount($x, $star);
        }
        $count = $this->collecStarCount();
        return $this->getListReview($info, $all_ids, $total, $limit, $offset, $count);
    }

    private function collecStarCount(){
        $collection = clone $this->builderQuery;
        $collection->getSelect()->reset(\Zend_Db_Select::LIMIT_COUNT);
        $collection->getSelect()->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $collection->load();// Reload the collection using the new SQL query:
        // $offset = 0;
        $count   = null;
        $star    = [];
        $star[0] = 0;
        $star[1] = 0;
        $star[2] = 0;
        $star[3] = 0;
        $star[4] = 0;
        $star[5] = 0;
        foreach ($collection as $entity) {
            $star[5] ++;
            $y = 0;
            foreach ($entity->getRatingVotes() as $vote) {
                $y += ($vote->getPercent() / 20);
            }
            $count = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')->countArray($entity->getRatingVotes());
            $count = $count == 0 ? 1 : $count;
            $x     = (int) ($y / $count);
            $z = $y % 3;
            $x = $z < 5 ? $x : $x + 1;
            $this->applyStarCount($x, $star);
        }
        return [
            '1_star' => $star[0],
            '2_star' => $star[1],
            '3_star' => $star[2],
            '4_star' => $star[3],
            '5_star' => $star[4],
        ];
    }
    
    private function setPageSize($collection, $parameters, &$limit, &$offset, &$page)
    {
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }
        if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
            $limit = $parameters[self::LIMIT];
        }
        $offset = $limit * ($page - 1) + 1;
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        $collection->getSelect()->limit($limit, $offset);
    }
    
    private function applyStarCount($x, &$star)
    {
        switch ($x) {
            case 1:
                $star[0] ++;
                break;
            case 2:
                $star[1] ++;
                break;
            case 3:
                $star[2] ++;
                break;
            case 4:
                $star[3] ++;
                break;
            case 5:
                $star[4] ++;
                break;
            case 0:
                $star[5] --;
                break;
            default:
                break;
        }
    }

    /**
     * @return array
     * override
     */
    public function show()
    {
        $entity     = $this->builderQuery;
        $data       = $this->getData();
        $parameters = $data['params'];
        $fields     = [];
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info = $entity->toArray($fields);
        return $this->getDetail($info);
    }
}
