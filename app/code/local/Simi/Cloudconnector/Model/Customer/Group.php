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
 * @category    Magestore
 * @package     Magestore_Connector
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Model Customer
 *
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
class Simi_Cloudconnector_Model_Customer_Group extends Simi_Cloudconnector_Model_Abstract
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
        $groupId = $data['customer-groups'];
        $params = array();
        if (isset($data['params']))
            $params = $data['params'];
        if (!$groupId) {
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListCustomerGroup($offset, $limit, $update, $count, $params);
        } else {
            $information = $this->getCustomerGroup($groupId);
        }
        return $information;
    }

    /**
     * get customer group collection
     *
     * @param   boolean
     * @return   object
     */
    public function getCustomerGroupCollection($update)
    {
        $customerGroups = Mage::getModel('customer/group')->getCollection();
        if ($update) {
            $customerGroups->getSelect()->join(array('sync' => $customerGroups->getTable('cloudconnector/sync')),
                'main_table.customer_group_id = sync.element_id', array('*'));
            $customerGroups->getSelect()->where('sync.type =' . self::TYPE_CUSTOMER_GROUP);
        }
        return $customerGroups;
    }

    /**
     * get customer group list
     *
     * @param   int , int, boolean, boolean, array
     * @return   json
     */
    public function getListCustomerGroup($offset, $limit, $update, $count, $params)
    {
        $customerGroups = $this->getCustomerGroupCollection($update);
        if ($count)
            return $customerGroups->getSize();
        if (!$offset)
            $offset = 0;
        if (!$limit)
            $limit = 10;
        $customerGroups->setPageSize($limit);
        $customerGroups->setCurPage($offset / $limit + 1);
        if ($params)
            foreach ($params as $key => $value) {
                $customerGroups->addFieldToFilter($key, $value);
            }
        $groupList = array();
        foreach ($customerGroups as $group) {
            $groupInfo = array(
                'id' => $group->getId(),
                'name' => $group->getData('customer_group_code'),
                'status' => 1,
                'tax_class_id' => $group->getData('tax_class_id'),
                'updated_at' => now(),
                'created_at' => now()
            );
            $groupList[] = $groupInfo;
            if ($update) {
                $this->removeUpdateRecord($group->getData('id'));
            }
        }
        return $groupList;
    }

    /**
     * get customer group information
     *
     * @param   int
     * @return   json
     */
    public function getCustomerGroup($groupId)
    {
        $group = Mage::getModel('customer/group')->load($groupId);
        if (isset($offset) && ++$check_offset <= $offset) {
            continue;
        }
        if (isset($limit) && ++$check_limit > $limit)
            break;
        $groupInfo = array(
            'id' => $group->getId(),
            'name' => $group->getData('customer_group_code'),
            'status' => 1,
            'tax_class_id' => $group->getData('tax_class_id'),
            'updated_at' => now(),
            'created_at' => now()
        );
        return array($groupInfo);
    }


    /**
     * pull data from cloud
     *
     * @param   array
     * @return
     */
    public function pull($data)
    {
        $this->createCustomerGroup($data);
    }

    /**
     * create customer group
     *
     * @param   json
     * @return   json
     */
    public function createCustomerGroup($data)
    {
        $groupId = $data['id'];
        if ($groupId) {
            $group = Mage::getModel('customer/group')->load($groupId);
        } else {
            $group = Mage::getModel('customer/group');
        }
        $group->setCode($data['code']);
        $group->setTaxClassId($data['tax_class']);
        try {
            $group->save();
            return array('group_id' => $group->getId());
        } catch (Exception $e) {
            $message = $e->getMessage();
            $result = array('code' => $e->getCode(),
                'message' => $message);
            $information = array('errors' => $result);
            return $information;
        }
    }

}