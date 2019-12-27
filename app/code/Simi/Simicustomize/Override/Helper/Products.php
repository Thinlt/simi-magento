<?php

namespace Simi\Simicustomize\Override\Helper;


class Products extends \Simi\Simiconnector\Helper\Products
{
    //add vendor option to filter
    public function _filterByAtribute($collection, $attributeCollection, &$titleFilters, &$layerFilters, $arrayIDs)
    {
        foreach ($attributeCollection as $attribute) {
            $attributeValues  = $collection->getAllAttributeValues($attribute->getAttributeCode());
            $this->addFilterByAttribute($attribute, $attributeValues, $layerFilters, $titleFilters, $arrayIDs);
        }
        $vendorAtt = $this->simiObjectManager
                ->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection')
                ->addFieldToFilter('attribute_code', 'vendor_id')
                ->getFirstItem();
        $vendorHelper = $this->simiObjectManager->get('\Simi\Simicustomize\Helper\Vendor');
        if ($vendorAtt->getId()) {
            $vendors = $this->simiObjectManager->get('\Vnecoms\Vendors\Model\Vendor')
                ->getCollection();
            $options = array();
            $attributeValues  = $this->getAllAttributeValues($collection, $vendorAtt->getAttributeCode());
            foreach ($vendors as $vendor) {
                $profile = $vendorHelper->getProfile($vendor->getId());
                $options[] = array(
                    'label' => ($profile && isset($profile['store_name']))?$profile['store_name']:$vendor->getId(),
                    'value' => $vendor->getId(),
                );
            }
            $this
                ->addFilterByAttribute($vendorAtt, $attributeValues, $layerFilters, $titleFilters, $arrayIDs, $options);
        }
    }


    protected function getAllAttributeValues($collection, $attribute)
    {
        $select = clone $collection->getSelect();
        $data = $collection->getConnection()->fetchAll($select);
        $res = [];
        foreach ($data as $row) {
            $res[$row['entity_id']][0] = $row['vendor_id'];
        }
        return $res;
    }

    protected function addFilterByAttribute($attribute, $attributeValues, &$layerFilters, &$titleFilters, $arrayIDs, $options = null) {
        $attributeOptions = [];
        if (in_array($attribute->getDefaultFrontendLabel(), $titleFilters)) {
            return;
        }
        foreach ($attributeValues as $productId => $optionIds) {
            if (isset($optionIds[0]) && isset($arrayIDs[$productId]) && ($arrayIDs[$productId] != null)) {
                $optionIds = explode(',', $optionIds[0]);
                foreach ($optionIds as $optionId) {
                    if (isset($attributeOptions[$optionId])) {
                        $attributeOptions[$optionId] ++;
                    } else {
                        $attributeOptions[$optionId] = 1;
                    }
                }
            }
        }

        if (!$options)
            $options = $attribute->getSource()->getAllOptions();
        $filters = [];
        foreach ($options as $option) {
            if (isset($option['value']) && isset($attributeOptions[$option['value']])
                && $attributeOptions[$option['value']]) {
                $option['count'] = $attributeOptions[$option['value']];
                $filters[]       = $option;
            }
        }

        if ($this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')->countArray($filters) >= 1) {
            $titleFilters[] = $attribute->getDefaultFrontendLabel();
            $layerFilters[] = [
                'attribute' => $attribute->getAttributeCode(),
                'title'     => $attribute->getDefaultFrontendLabel(),
                'filter'    => $filters,
            ];
        }
    }
}
