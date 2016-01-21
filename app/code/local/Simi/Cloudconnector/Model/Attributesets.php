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
 * @package     Simi_Cloudconnector
 * @copyright   Copyright (c) 2015 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Model Customer
 *
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
class Simi_Cloudconnector_Model_Attributesets extends Simi_Cloudconnector_Model_Abstract
{

    /**
     * Internal constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * get api result
     *
     * @param   array
     * @return   json
     */
    public function run($data)
    {
        $attributesetId = $data['attributesets'];
        $params = array();
        if (isset($data['params']))
            $params = $data['params'];
        if (!$attributesetId) {
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListAttributesets($offset, $limit, $update, $count, $params);
        } else {
            $information = $this->getAttributeset($attributesetId);
        }
        return $information;
    }

    /**
     * get attribute collection
     *
     * @param   boolean
     * @return   object
     */
    public function getAttributesetCollection($update)
    {
        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId();
        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityTypeId);
        if ($update) {
            $attributeSetCollection->getSelect()->join(array('sync' => $attributeSetCollection->getTable('cloudconnector/sync')),
                'main_table.attribute_set_id = sync.element_id', array('*'));
            $attributeSetCollection->getSelect()->where('sync.type =' . self::TYPE_ATTRIBUTESET);
        }
        return $attributeSetCollection;
    }

    /**
     * get attributesets list
     *
     * @param   int , int, boolean, boolean, array
     * @return   json
     */
    public function getListAttributesets($offset, $limit, $update, $count, $params)
    {
        $attributeSetCollection = $this->getAttributesetCollection($update);
        if ($count)
            return $attributeSetCollection->getSize();
        if (!$offset)
            $offset = 0;
        if (!$limit)
            $limit = 10;
        $attributeSetCollection->setPageSize($limit);
        $attributeSetCollection->setCurPage($offset / $limit + 1);
        if ($params)
            foreach ($params as $key => $value) {
                $attributeSetCollection->addFieldToFilter($key, $value);
            }
        $attributeSetList = array();
        foreach ($attributeSetCollection as $attributeSet) {
            $attributeSetList[] = $this->getAttributeSetInfo($attributeSet);
            if ($update) {
                $this->removeUpdateRecord($attributeSet->getData('id'));
            }
        }
        return $attributeSetList;
    }

    /**
     * get attributeset information
     *
     * @param   int
     * @return   json
     */
    public function getAttributeset($attributesetId)
    {
        $attributeSet = Mage::getModel("eav/entity_attribute_set")->load($attributesetId);
        if ($attributeSet->getId()) {
            $attributeSetInfo = $this->getAttributeSetInfo($attributeSet);
        }
        return array($attributeSetInfo);
    }

    /**
     * get attributeset information
     *
     * @param   object
     * @return   json
     */
    public function getAttributeSetInfo($attributeSet)
    {
        $attributeSetInfo = array();
        $attributeSetInfo['id'] = $attributeSet->getId();
        $attributeSetInfo['name'] = $attributeSet->getData('attribute_set_name');
        $attributeSetInfo['updated_at'] = now();
        $attributeSetInfo['created_at'] = now();
        $attributeSetInfo['attributes'] = $this->getAttributeInAttributeSet($attributeSet->getId());

        return $attributeSetInfo;
    }

    /**
     * get attributes of an attributeset
     *
     * @param   object
     * @return   json
     */
    public function getAttributeInAttributeSet($attributeSetId)
    {
        $mainAttributes = array('sku', 'name', 'type_id', 'entity_type_id', 'entity_id', 'price', 'final_price', 'attribute_set_id',
            'description', 'short_description', 'status', 'created_at',
            'updated_at', 'tax_class_id', 'attribute_set_id',
            'minimal_price', 'updated_at', 'thumbnail', 'small_image', 'visibility',
            'media_gallery', 'media', 'weight', 'length', 'width', 'height', 'image',
            'options_container', 'page_layout, custom_design'
        );
        $attributes = Mage::getModel('catalog/product_attribute_api')->items($attributeSetId);

        $attributeList = array();
        foreach ($attributes as $attribute) {
            if (!in_array($attribute['code'], $mainAttributes))
                $attributeList[] = $attribute['attribute_id'];
        }
        return $attributeList;
    }

}