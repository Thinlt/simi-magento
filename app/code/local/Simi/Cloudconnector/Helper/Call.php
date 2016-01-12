<?php

class Simi_Cloudconnector_Helper_Call extends Mage_Core_Helper_Abstract
{
    public $simi_url;

    public function __construct()
    {
        $this->setSimiUrl('http://requestb.in/zp3zqxzp');
    }

    /**
     * create request || application/json
     * @param $method
     * @param $url
     * @param $args
     * @param $isSentBody
     * @param $content
     * @param $cert
     * @return resource
     */
    public function sendRequest($method, $url, $args, $isSentBody, $content = 'default', $key, $cert = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($method == 'POST')
            curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        if ($isSentBody) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content:' . $content,
                'X-Request-Signature:' . self::signature($args, $key),
            ));
        }
        if ($cert)
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . $cert);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        try {
            return curl_exec($ch);
        } catch (Exception $e) {
            throw $e;
        }
    }



    public function getSimiUrl()
    {
        return $this->simi_url;
    }

    public function setSimiUrl($url)
    {
        $this->simi_url = $url;
    }

    public function signature($data, $key)
    {
        $signature = base64_encode(hash_hmac('sha256', json_encode($data), $key, true));
        return $signature;
    }
}