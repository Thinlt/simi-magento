<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package    Simi_Cloudconnector
 * @copyright    Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license    http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Model
 *
 * @category    Simi
 * @package    Simi_Cloudconnector
 * @author    Simi Developer
 */
class Simi_Cloudconnector_Model_Catalog_Category extends Simi_Cloudconnector_Model_Abstract
{

    /**
     * Internal constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * get cloudconnector helper
     *
     * @param
     * @return   Simi_Cloudconnector_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('cloudconnector');
    }

    /**
     * get api result
     *
     * @param   array
     * @return   json
     */
    public function run($data)
    {
        $categoryId = $data['categories'];
        $params = array();
        if (isset($data['params']))
            $params = $data['params'];
        if ($categoryId) {
            $information = $this->getCategoryInfo($categoryId);
        } else {
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListCategory($offset, $limit, $update, $count, $params);
        }
        return $information;
    }

    /**
     * get category collection
     *
     * @param   boolean
     * @return   object
     */
    public function getCategoryCollection($update)
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->addAttributeToSelect('description');
        if ($update) {
            $collection->getSelect()->join(array('sync' => $collection->getTable('cloudconnector/sync')),
                'e.entity_id = sync.element_id', array('*'));
            $collection->getSelect()->where('sync.type =' . self::TYPE_CATALOG_CATEGORY);
        }
        return $collection;
    }

    /**
     * get categories list
     *
     * @param   int , int, boolean, boolean, array
     * @return   json
     */
    public function getListCategory($offset, $limit, $update, $count, $params)
    {
        $categories = $this->getCategoryCollection($update);
        if ($count)
            return $categories->getSize();
        if (!$offset)
            $offset = 0;
        if (!$limit)
            $limit = 10;
        $categories->setPageSize($limit);
        $categories->setCurPage($offset / $limit + 1);
        if ($params)
            foreach ($params as $key => $value) {
                $categories->addFieldToFilter($key, $value);
            }
        $categoryList = array();
        foreach ($categories as $category) {
            $categoryInfo = $this->getInfo($category);
            $categoryList[] = $categoryInfo;
            if ($update) {
                $this->removeUpdateRecord($category->getData('id'));
            }
        }
        return $categoryList;
    }

    /**
     * get category information
     *
     * @param   int
     * @return   json
     */
    public function getCategoryInfo($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $categoryInfo = $this->getInfo($category);
        return array($categoryInfo);
    }

    /**
     * get json information of a category
     *
     * @param   object
     * @return   json
     */
    public function getInfo($category)
    {
        $categoryInfo = array();
        $categoryInfo['id'] = $category->getId();
        $categoryInfo['name'] = $category->getData('name');
        $categoryInfo['slug'] = $category->getData('name');
        $categoryInfo['description'] = $category->getData('description');
        $categoryInfo['parent'] = $category->getData('parent_id');
        $categoryInfo['updated_at'] = $category->getData('created_at');
        $categoryInfo['created_at'] = $category->getData('updated_at');
        $categoryInfo['status'] = $category->getData('is_active');
        if ($category->getData('children_count') > 0)
            $categoryInfo['has_children'] = true;
        else
            $categoryInfo['has_children'] = false;
        return $categoryInfo;
    }

    /**
     * pull data from cloud
     *
     * @param   array
     * @return
     */
    public function pull($data)
    {
        return $this->createCategory($data);
    }

    /**
     * create customer group
     *
     * @param   json
     * @return   json
     */
    public function createCategory($data)
    {
        $categoryId = $data['id'];
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
        } else {
            $category = Mage::getModel('catalog/category');
        }
        $category->setName($data['name']);
        $category->setUrlKey($data['name']);
        $category->setIsActive($data['status']);
        $category->setDisplayMode('PRODUCTS');
        $category->setStoreId(Mage::app()->getStore()->getId());
        if (isset($data['parent_id']) && !empty($data['parent_id'])) {
            $parentCategory = Mage::getModel('catalog/category')->load($data['parent_id']);
            $category->setPath($parentCategory->getPath());
        } else {
            $parentCategory = Mage::getModel('catalog/category')->load(1);
            $category->setPath($parentCategory->getPath());
        }
        try {
            $category->save();
            return array('category_id' => $category->getId());
        } catch (Exception $e) {
            $message = $e->getMessage();
            $result = array('code' => $e->getCode(),
                'message' => $message);
            $information = array('errors' => $result);
            return $information;
        }

    }
}

