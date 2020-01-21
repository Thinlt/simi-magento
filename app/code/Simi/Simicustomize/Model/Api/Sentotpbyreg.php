<?php

namespace Simi\Simicustomize\Model\Api;


class Sentotpbyreg extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function setBuilderQuery()
    {
        // TODO: Implement setBuilderQuery() method.
    }

    public function index()
    {
        $data = $this->getData();
        $result = null;
        $dataHelper = $this->simiObjectManager->get(\Magecomp\Mobilelogin\Helper\Data::class);
        if (isset($data['params']['mobile']) && isset($data['params']['website_id'])) {
            $mobile = $data['params']['mobile'];
            $website_id = $data['params']['website_id'];
            $result = $dataHelper->sendOTPCode($mobile, $website_id);
        }
        return [
            'result' => $result
        ];
    }
}
