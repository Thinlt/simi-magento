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
 * Connector Helper
 *
 * @category    Simi
 * @package    Simi_Cloudconnector
 * @author    Simi Developer
 */
class Simi_Cloudconnector_Helper_Customer extends Mage_Core_Helper_Abstract
{

    /**
     * convert customer address
     *
     * @param    array , customer
     * @return   array
     */
    public function getAddress($data, $customer)
    {
        return array(
            'first_name' => $customer->getFirstname(),
            'last_name' => $customer->getLastname(),
            'street' => $data->getStreet(),
            'city' => $data->getCity(),
            'state' => $data->getRegionId(),
            'zip' => $data->getPostcode(),
            'country' => $data->getCountryId(),
            'phone' => $data->getTelephone(),
            'adddress_email' => $customer->getEmail(),
            'adddress_id' => $data->getId(),
        );
    }

    /**
     * convert customer address to order
     *
     * @param    array , object
     * @return   array
     */
    public function getAddressToOrder($data, $customer)
    {
        $street = $data->getStreet();
        $address = array(
            'address_id'=>$data->getId(),
            'first_name' => $data->getFirstname() != NULL ? $data->getFirstname() : "",
            'middlename' => $data->getMiddlename() != NULL ? $data->getMiddlename() : "",
            'last_name' => $data->getLastname() != NULL ? $data->getLastname() : "",
            'prefix' => $data->getPrefix() != NULL ? $data->getPrefix() : "",
            'suffix' => $data->getSuffix() != NULL ? $data->getSuffix() : "",
            'vat_id' => $data->getVatId() != NULL ? $data->getVatId() : "",
            'street' => $street[0],
            'city' => $data->getCity() != NULL ? $data->getCity() : "",
            'state' => array(
                'code' => $data->getRegionCode() != NULL ? $data->getRegionCode() : "",
                'name' => $data->getRegion() != NULL ? $data->getRegion() : "",
            ),
            'country' => array(
                'code' => $data->getCountry() != NULL ? $data->getCountry() : "",
                'name' => $data->getCountryModel()->loadByCode($data->getCountry())->getName(),
            ),
            'zip' => $data->getPostcode() != NULL ? $data->getPostcode() : "",
            'phone' => $data->getTelephone() != NULL ? $data->getTelephone() : "",
            'email' => $data->getEmail() != NULL ? $data->getEmail() : "",
            'fax' => $data->getFax() != NULL ? $data->getFax() : "",
            'company' => $data->getCompany() != NULL ? $data->getCompany() : "",
        );
        if ($data->getId() == $customer->getDefaultBilling())
            $address['default_billing'] = 1;
        if ($data->getId() == $customer->getDefaultShipping())
            $address['default_shipping'] = 1;
        return $address;
    }
}