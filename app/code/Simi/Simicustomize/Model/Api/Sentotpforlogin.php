<?php

namespace Simi\Simicustomize\Model\Api;

class Sentotpforlogin extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function setBuilderQuery()
    {
        // TODO: Implement setBuilderQuery() method.
    }
    public function index()
    {
        $data       = $this->getData();
        $dataHelper = $this->simiObjectManager->get(\Magecomp\Mobilelogin\Helper\Data::class);
        $result     = null;
        if (isset($data['params']['mobile']) && isset($data['params']['website_id'])) {
            $mobile     = $data['params']['mobile'];
            $website_id = $data['params']['website_id'];
            $result     = $dataHelper->sendLoginOTPCode($mobile, $website_id);
        }
        return [
            'result' => $result
        ];
    }
}
