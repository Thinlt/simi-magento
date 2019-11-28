<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simicustomize\Model\Api;

class HomeNewcollections extends \Simi\Simiconnector\Model\Api\Apiabstract
{

    public $DEFAULT_ORDER = 'sort_order';
    const DEFAULT_LIMIT = 9999;

    public $visible_array;

    public function setSingularKey($singularKey)
    {
        if ($singularKey != 'HomeNewcollections') {
            $this->singularKey = 'HomeNewcollections';
        }
        return $this;
    }

    public function setBuilderQuery()
    {
        $data = $this->getData();
        if ($data['resourceid']) {
            $this->builderQuery = $this->simiObjectManager
                    ->get('Simi\Simicustomize\Model\Newcollections')->load($data['resourceid']);
        } else {
            $this->builderQuery = $this->getCollection();
        }
    }

    public function getCollection()
    {
        $typeID             = $this->simiObjectManager
                ->get('Simi\Simiconnector\Helper\Data')->getVisibilityTypeId('homecategory');
        $visibilityTable    = $this->resource->getTableName('simiconnector_visibility');
        $this->builderQuery = $this->simiObjectManager
            ->get('Simi\Simicustomize\Model\Newcollections')->getCollection()->addFieldToFilter('status', '1')
            ->applyAPICollectionFilter(
                $visibilityTable, $typeID, 
                $this->storeManager->getStore()->getId()
            );
        return $this->builderQuery;
    }

    public function index()
    {
        $result = parent::index();
        $data   = $this->getData();

        foreach ($result[$this->getPluralKey()] as $index => $item) {
            try {
                $imagesize                     = getimagesize(BP . '/pub/media/' . $item['newcollections_filename_0']);
                $item['newcollections_filename_0_width']  = $imagesize[0];
                $item['newcollections_filename_0_height'] = $imagesize[1];
                $item['newcollections_filename_0'] = $this->getMediaUrl($item['newcollections_filename_0']);
                $imagesize                     = getimagesize(BP . '/pub/media/' . $item['newcollections_filename_1']);
                $item['newcollections_filename_1_width']  = $imagesize[0];
                $item['newcollections_filename_1_height'] = $imagesize[1];
                $item['newcollections_filename_1'] = $this->getMediaUrl($item['newcollections_filename_1']);
                $imagesize                     = getimagesize(BP . '/pub/media/' . $item['newcollections_filename_2']);
                $item['newcollections_filename_2_width']  = $imagesize[0];
                $item['newcollections_filename_2_height'] = $imagesize[1];
                $item['newcollections_filename_2'] = $this->getMediaUrl($item['newcollections_filename_2']);
                $imagesize                     = getimagesize(BP . '/pub/media/' . $item['newcollections_filename_3']);
                $item['newcollections_filename_3_width']  = $imagesize[0];
                $item['newcollections_filename_3_height'] = $imagesize[1];
                $item['newcollections_filename_3'] = $this->getMediaUrl($item['newcollections_filename_3']);
            } catch (\Exception $e) {
                $item['function_warning'] = true;
            }

            $categoryModel    = $this->loadCategoryWithId($item['category_id_0']);
            $item['url_path_0'] = $categoryModel->getUrlPath();
            $item['cat_name_0'] = $categoryModel->getName();
            $categoryModel    = $this->loadCategoryWithId($item['category_id_1']);
            $item['url_path_1'] = $categoryModel->getUrlPath();
            $item['cat_name_1'] = $categoryModel->getName();
            $categoryModel    = $this->loadCategoryWithId($item['category_id_2']);
            $item['url_path_2'] = $categoryModel->getUrlPath();
            $item['cat_name_2'] = $categoryModel->getName();
            $categoryModel    = $this->loadCategoryWithId($item['category_id_3']);
            $item['url_path_3'] = $categoryModel->getUrlPath();
            $item['cat_name_4'] = $categoryModel->getName();
            $result[$this->getPluralKey()][$index] = $item;
        }
        return $result;
    }

    public function getDefaultLimit() {
        return self::DEFAULT_LIMIT;
    }

    public function loadCategoryWithId($id)
    {
        $categoryModel    = $this->simiObjectManager
                ->create('\Magento\Catalog\Model\Category')->load($id);
        return $categoryModel;
    }
}
