<?php

/**
 * Copyright © 2019 Simi. All rights reserved.
 */

namespace Simi\Simicustomize\Model;

class Customer extends \Simi\Simiconnector\Model\Customer
{
    /*
     * Social Login (post method)
     * @param 
     * $data - Object with at least:
     * $data['contents']->email
     * $data['contents']->password
     * $data['contents']->firstname
     * $data['contents']->lastname
     * $data['contents']->telephone
     */

    public function socialLogin($data)
    {
        $data = (object) $data['contents'];
        if (!$data->email) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Cannot Get Your Email. Please let your application provide an email to login.'), 4);
        }
        $customer = $this->simiObjectManager
            ->get('Simi\Simicustomize\Override\Helper\Customer')->getCustomerByEmail($data->email);
        if ($customer->getId()) {
            if (!isset($data->password)) {
                throw new \Simi\Simiconnector\Helper\SimiException(__('Password is not Valid'), 4);
            }
            if (!$this->simiObjectManager
                ->get('Simi\Simicustomize\Override\Helper\Customer')->validateSimiPass($data->email, $data->password, 'social_login')) {
                if (!$customer->validatePassword($data->password)) {
                    throw new \Simi\Simiconnector\Helper\SimiException(__('Your email has been used to log into our application by another social network.'), 4);
                }
            }
        } else {
            if (!$data->firstname) {
                $data->firstname = __('Firstname');
            }
            if (!$data->lastname) {
                $data->lastname = __('Lastname');
            }
            $customer = $this->_createCustomer($data);
        }
    }
}
