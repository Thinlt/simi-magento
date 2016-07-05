<?php

/**
 * Created by PhpStorm.
 * User: Scott
 * Date: 1/22/2016
 * Time: 11:24 AM
 */
class Simi_Simibooking_Model_Catalog_Product_Options_Bookable extends Simi_Connector_Model_Abstract
{
    public $_product;

    public function getOptions($product)
    {
        $this->_product = $product;
        $infomation = array();
        $this->addOption($infomation);
        return $infomation;
    }

    public function addOption(&$infomation, $product = null)
    {
        if ($product == null) {
            $product = $this->_product;
        } else {
            $this->_product = $product;
        }

        foreach ($product->getOptions() as $_option) {
            $type = '';
            if ($_option->getType() == 'multiple' || $_option->getType() == 'checkbox') {
                $type = 'multi';
            } elseif ($_option->getType() == 'drop_down' || $_option->getType() == 'radio') {
                $type = 'single';
            }
            /* @var $option Mage_Catalog_Model_Product_Option */
            if ($_option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                foreach ($_option->getValues() as $value) {
                    /* @var $value Mage_Catalog_Model_Product_Option_Value */
                    $info = array(
                        'option_id' => $value->getId(),
                        'option_value' => $value->getTitle(),
                        'option_price' => Mage::helper('core')->currency($value->getPrice(true), false, false),
                        'option_title' => $_option->getTitle(),
                        'position' => $_option->getSortOrder(),
                        'option_type_id' => $_option->getId(),
                        'option_type' => $type,
                        'is_required' => $_option->getIsRequire() == 1 ? 'YES' : 'No',
                    );

                    $this->setOptionPriceTax($info, $value->getPrice(true));
                    $infomation[] = $info;
                }
            } else if ($_option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_TEXT) {
                $info = array(
                    'option_price' => Mage::helper('core')->currency($_option->getPrice(true), false, false),
                    'option_title' => $_option->getTitle(),
                    'position' => $_option->getSortOrder(),
                    'option_type_id' => $_option->getId(),
                    'option_type' => 'text',
                    'is_required' => $_option->getIsRequire() == 1 ? 'YES' : 'No',
                );

                $this->setOptionPriceTax($info, $_option->getPrice(true));
                $infomation[] = $info;
            } else if ($_option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_DATE) {
                $info = array(
                    'option_price' => Mage::helper('core')->currency($_option->getPrice(true), false, false),
                    'option_title' => $_option->getTitle(),
                    'position' => $_option->getSortOrder(),
                    'option_type_id' => $_option->getId(),
                    'option_type' => $_option->getType(),
                    'is_required' => $_option->getIsRequire() == 1 ? 'YES' : 'NO',
                );

                $this->setOptionPriceTax($info, $_option->getPrice(true));
                $infomation[] = $info;
            }
        }

    }
}