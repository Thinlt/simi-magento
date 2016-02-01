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
class Simi_Cloudconnector_Model_Customer extends Simi_Cloudconnector_Model_Abstract
{

    /**
     * Internal constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * get helper customer
     *
     * @param
     * @return   Simi_Cloudconnector_Helper_Customer
     */
    protected function _helperCustomer()
    {
        return Mage::helper('cloudconnector/customer');
    }

    /**
     * get api result
     *
     * @param   array
     * @return   json
     */
    public function run($data)
    {
        $customerId = $data['customers'];
        $params = array();
        if (isset($data['params']))
            $params = $data['params'];
        if (!$customerId) {
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListCustomer($offset, $limit, $update, $count, $params);
        } else {
            $information = $this->getCustomer($customerId);
        }
        return $information;
    }

    /**
     * get customer collection
     *
     * @param   boolean
     * @return   object
     */
    public function getCustomerCollection($update)
    {
        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('default_billing')
            ->addAttributeToSelect('default_shipping')
            ->addAttributeToSelect(array('firstname', 'lastname', 'password_hash', 'gender',
                'middlename', 'prefix', 'suffix', 'taxvat', 'dob'));
        if ($update) {
            $customers->getSelect()->join(array('sync' => $customers->getTable('cloudconnector/sync')),
                'e.entity_id = sync.element_id', array('*'));
            $customers->getSelect()->where('sync.type =' . self::TYPE_CUSTOMER);
        }
        return $customers;
    }

    /**
     * get customers list
     *
     * @param   int , int, boolean, boolean, array
     * @return   json
     */
    public function getListCustomer($offset, $limit, $update, $count, $params)
    {
        $customers = $this->getCustomerCollection($update);
        if ($count)
            return $customers->getSize();
        if (!$offset)
            $offset = 0;
        if (!$limit)
            $limit = 10;
        $customers->setPageSize($limit);
        $customers->setCurPage($offset / $limit + 1);
        if ($params)
            foreach ($params as $key => $value) {
                $customers->addFieldToFilter($key, $value);
            }
        $customerList = array();
        foreach ($customers as $customer) {
            $customerInfo = $this->getInfo($customer);
            $customerList[] = $customerInfo;
            if ($update) {
                $this->removeUpdateRecord($customer->getData('id'));
            }
        }
        return $customerList;
    }

    /**
     * get customer address
     *
     * @param   int
     * @return   json
     */
    public function getCustomerAddress($customer)
    {
        $result = array();
        $adrressAddition = $customer->getAddresses();
        $list = array();
        $i = 0;
        foreach ($adrressAddition as $adrr) {
            $item = $this->_helperCustomer()->getAddress($adrr, $customer);
            $result[$i] = $this->_helperCustomer()
                ->getAddressToOrder($adrr, $customer, $address_billing_id, $address_shipping_id, $adrr->getId());
            $i++;
        }
        return $result;
    }

    /**
     * get customer information
     *
     * @param   int
     * @return   json
     */
    public function getCustomer($customerId)
    {
        $customer = Mage::getModel('customer/customer')->getCollection()
            ->addFieldToFilter('entity_id', $customerId)
            ->addAttributeToSelect('default_billing')
            ->addAttributeToSelect('default_shipping')
            ->addAttributeToSelect(array('firstname', 'lastname', 'password_hash', 'gender',
                'middlename', 'prefix', 'suffix', 'taxvat', 'dob'))
            ->getFirstItem();
        $customerInfo = $this->getInfo($customer);
        return array($customerInfo);
    }

    /**
     * get information detail
     *
     * @param   int
     * @return   json
     */
    public function getInfo($customer)
    {
        $customerInfo = array();
        $customerInfo['id'] = $customer->getId();
        $customerInfo['sync'] = true;
        $customerInfo['first_name'] = $customer->getData('firstname');
        $customerInfo['last_name'] = $customer->getData('lastname');
        $customerInfo['email'] = $customer->getData('email');
        $customerInfo['status'] = $customer->getData('is_active');
        $customerInfo['password'] = $customer->getData('password_hash');
        $customerInfo['store_id'] = $customer->getData('store_id');
        $customerInfo['group_id'] = $customer->getData('group_id');
        $customerInfo['updated_at'] = $customer->getData('updated_at');
        $customerInfo['created_at'] = $customer->getData('created_at');
        $customerInfo['middlename'] = $customer->getData('middlename') != NULL ? $customer->getData('middlename') : "";
        $customerInfo['taxvat'] = $customer->getData('taxvat') != NULL ? $customer->getData('taxvat') : "";
        $customerInfo['prefix'] = $customer->getData('prefix') != NULL ? $customer->getData('prefix') : "";
        $customerInfo['suffix'] = $customer->getData('suffix') != NULL ? $customer->getData('suffix') : "";
        $customerInfo['dob'] = $customer->getData('dob') != NULL ? $customer->getData('dob') : "";
        $customerInfo['gender'] = $customer->getData('gender') != NULL ? $customer->getData('gender') : "";
        $customerInfo['website_id'] = $customer->getData('website_id');
        $customerInfo['addresses'] = $this->getCustomerAddress($customer);
        return $customerInfo;
    }

    /**
     * check customer login from client
     *
     * @param   int
     * @return   json
     */
    public function updateUser($data)
    {
        $email = $data['email'];
        $customer = Mage::getModel('customer/customer')->loadByEmail($email);
        if ($customer->getId()) {
            if (isset($data['prefix']))
                $customer->setData('prefix', $data['prefix']);
            if (isset($data['firstname']))
                $customer->setData('firstname', $data['firstname']);
            if (isset($data['middlename']))
                $customer->setData('middlename', $data['middlename']);
            if (isset($data['lastname']))
                $customer->setData('lastname', $data['lastname']);
            if (isset($data['month']))
                $customer->setData('month', $data['month']);
            if (isset($data['day']))
                $customer->setData('day', $data['day']);
            if (isset($data['year']))
                $customer->setData('year', $data['year']);
            if (isset($data['taxvat']))
                $customer->setData('taxvat', $data['taxvat']);
            if (isset($data['gender']))
                $customer->setData('gender', $data['gender']);
            if (isset($data['password']))
                $customer->setData('password', $data['password']);
            try {
                $customer->save();
                return 1;
            } catch (Exception $e) {
                return 0;
            }
        }
        return 0;
    }

    /**
     * check customer login from client
     *
     * @param   int
     * @return   json
     */
    public function login($userEmail, $userPassword)
    {
        try {
            $this->loginCustomer($userEmail, $userPassword);
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * authenticate customer
     *
     * @param   string , string
     * @return   int
     */
    public function loginCustomer($username, $password)
    {
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        if ($customer->authenticate($username, $password)) {
            return $customer->getId;
        }
        return false;
    }

    /**
     * pull data from cloud
     *
     * @param   array
     * @return
     */
    public function pull($data)
    {
        $this->createCustomer($data);
    }

    /**
     * create customer group
     * @param   json
     * @return   json
     */
    public function createCustomer($data)
    {
        $customerId = $data['id'];
        if ($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
        } else {
            $customer = Mage::getModel('customer/customer');
        }
        $customer->setData($data);
        try {
            $customer->save();
            if ($customer->getId()) {
                if (!empty($data['addresses']))
                    $this->saveAddress($data['addresses'], $customer->getId());
            }
            return array('customer_id' => $customer->getId());
        } catch (Exception $e) {
            $message = $e->getMessage();
            $result = array('code' => $e->getCode(),
                'message' => $message);
            $information = array('errors' => $result);
            return $information;
        }
    }

    /**
     * create or update address
     * @param $addresses
     * @param $customer_id
     * @throws Exception
     */
    public function saveAddress($addresses, $customer_id)
    {
        foreach ($addresses as $data) {
            $addressId = $data['id'];
            if ($addressId) {
                $address = Mage::getModel('customer/address')->load($addressId);
            } else {
                $address = Mage::getModel('customer/address');
            }
            $address->setData($data)
                ->setCustomerId($customer_id);

            //shipping and billing
            if ($data['default_shipping'] == 1)
                $address->setIsDefaultShipping(1);
            if ($data['default_billing'] == 1)
                $address->setIsDefaultBilling('1');

            $address->setSaveInAddressBook('1');
            $address->save();
        }
    }
}