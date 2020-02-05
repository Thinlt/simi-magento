<?php

namespace Simi\Simicustomize\Model\Api;


class Verifyotpforregister extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function setBuilderQuery()
    {
        // TODO: Implement setBuilderQuery() method.
    }

    public function index()
    {
        $data = $this->getData();
        $mobile = $data['params']['mobile'];
        $otp = $data['params']['otp'];

        $otpModels = $this->simiObjectManager->get(\Magecomp\Mobilelogin\Model\RegotpmodelFactory::class)->create();
        $collection = $otpModels->getCollection();
        $collection->addFieldToFilter('mobile', $mobile);
        $collection->addFieldToFilter('random_code', $otp);
        $result = 'false';
        if (count($collection) == '1') {
            $result = "true";
        }

        return [
            'result' => $result
        ];
    }
}
