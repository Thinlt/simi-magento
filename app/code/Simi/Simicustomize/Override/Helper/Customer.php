<?php

namespace Simi\Simicustomize\Override\Helper;

class Customer extends \Simi\Simiconnector\Helper\Customer{
    
    /* override this function to add data for new attribute mobilephone when customer register new account 
     * $customer - Customer Model
     * $data - Data Object
     * a. Magento\Customer\Model\Data\Customer
     * b. Magento\Customer\Model\Customer
     *
     */

    public function applyDataToCustomer(&$customer, $data)
    {
        if (isset($data->day) && $data->day != "") {
            $birthday = $data->year . "-" . $data->month . "-" . $data->day;
            $customer->setDob($birthday);
        }

        if (isset($data->taxvat)) {
            $customer->setTaxvat($data->taxvat);
        }

        if (isset($data->gender) && $data->gender) {
            $customer->setGender($data->gender);
        }
        if (isset($data->prefix) && $data->prefix) {
            $customer->setPrefix($data->prefix);
        }

        if (isset($data->middlename) && $data->middlename) {
            $customer->setMiddlename($data->middlename);
        }

        if (isset($data->suffix) && $data->suffix) {
            $customer->setSuffix($data->suffix);
        }
        if (isset($data->telephone) && $data->telephone) {
            $customer->setCustomAttribute('mobilenumber',$data->telephone);
        }
//        if (!isset($data->password)) {
//            $encodeMethod = 'md5';
//            $data->password = 'simipassword'
//                    . rand(pow(10, 9), pow(10, 10)) . substr($encodeMethod(microtime()), rand(0, 26), 5);
//        }
    }

}