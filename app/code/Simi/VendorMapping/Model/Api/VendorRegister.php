<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\VendorMapping\Model\Api;

use Simi\VendorMapping\Api\VendorRegisterInterface;
use Vnecoms\Vendors\Controller\Seller\RegisterPost;

class VendorRegister extends RegisterPost implements VendorRegisterInterface
{
    public function registerPost(){
        if (!$this->_vendorHelper->moduleEnabled()) {
            return [[
                'status' => 'error',
                'message' => __('Module not enabled.')
            ]];
        }

        if (!$this->_vendorHelper->isEnableVendorRegister()) {
            return [[
                'status' => 'error',
                'message' => __("You don't have permission to access this page")
            ]];
        }
        
        $vendorData = $this->getRequest()->getPost();
        
        if (!isset($vendorData['username'])) {
            try{
                $requestBody = $this->getRequest()->getContent();
                $vendorData = json_decode($requestBody, true);
            }catch(\Exception $e){
                return [[
                        'status' => 'error',
                        'message' => __('Request body not a json string.')
                ]];
            }
        }

        if ($vendorData && is_array($vendorData)) {
            try {
                $vendor = $this->_vendorFactory->create();
                $vendor->setData($vendorData);
                $vendor->setGroupId($this->_vendorHelper->getDefaultVendorGroup());
            
                $customer = $this->_vendorSession->getCustomer();
                $vendor->setCustomer($customer);
                $vendor->setWebsiteId($customer->getWebsiteId());
                
                if ($this->_vendorHelper->isRequiredVendorApproval()) {
                    $vendor->setStatus(Vendor::STATUS_PENDING);
                    $message = __("Your seller account has been created and awaiting for approval.");
                } else {
                    $vendor->setStatus(Vendor::STATUS_APPROVED);
                    $message = __("Your seller account has been created.");
                }
                
                $errors = $vendor->validate();
                
                if ($errors !== true) {
                    return [[
                        'status' => 'error',
                        'message' => __(implode(", ", $errors))
                    ]];
                }
                
                $vendor->save();

                if($this->_vendorHelper->isUsedCustomVendorUrl()){
                    return [[
                        'status' => 'error',
                        'message' => __('Your seller account has been created. You can now login to vendor panel.')
                    ]];
                }

                return [[
                    'status' => 'success',
                    'message' => __($message)
                ]];
            } catch (\Exception $e) {
                $this->_messageManager->addError($e->getMessage());
                $this->_vendorSession->setFormData($vendorData);
                return [[
                    'status' => 'error',
                    'message' => __($e->getMessage())
                ]];
            }
        } else {
            return [
                [
                    'status' => 'error',
                    'message' => __('POST data is invalid')
                ]
            ];
        }

        return [
            [
                'status' => 'error',
                'message' => __('Register error')
            ]
        ];
    }
}
