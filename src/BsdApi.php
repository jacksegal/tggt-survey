<?php

namespace Bsd\FormBuilder;


class BsdApi
{
    protected $urlRoot;
    protected $appSecret;
    protected $appId;

    public function __construct($config)
    {
        $this->urlRoot = $config['urlRoot'];
        $this->appSecret = $config['appSecret'];
        $this->appId = $config['appId'];
    }

    public function email_unsubscribe($email, $reason)
    {
        $apiTs = time();
        $url = '/page/api/cons/email_unsubscribe';
        $getParams = 'api_ver=2&api_id=' . $this->appId . '&api_ts=' . $apiTs . '&email=' . $email . '&reason=' . $reason;

        return ($this->call_bsd($apiTs, $url, $getParams, false));
    }

    public function list_form_fields($signupFormId)
    {
        $apiTs = time();
        $url = '/page/api/signup/list_form_fields';
        $getParams = 'api_ver=2&api_id=' . $this->appId . '&api_ts=' . $apiTs . '&signup_form_id=' . $signupFormId;

        return ($this->call_bsd($apiTs, $url, $getParams, false));
    }

    public function process_signup($signupFormId, $signupData)
    {
        $apiTs = time();
        $url = '/page/api/signup/process_signup';
        $getParams = 'api_ver=2&api_id=' . $this->appId . '&api_ts=' . $apiTs;

        $signupFormFields = '';

        foreach ($signupData as $data) {
            $signupFormFields .= '<signup_form_field id="' . $data['id'] . '">' . $data['value'] . '</signup_form_field>';
        }

        $postData = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<api>
   <signup_form id="$signupFormId">
      $signupFormFields
   </signup_form>
</api>
EOT;

        return ($this->call_bsd($apiTs, $url, $getParams, $postData));
    }

    public function call_bsd($apiTs, $url, $getParams, $postData = false)
    {
        $mac = $this->create_mac($apiTs, $url, $getParams);
        return ($this->do_curl($url, $mac, $postData));
    }

    private function create_mac($apiTs, $formUrl, $formParams)
    {
        $signingString = $this->appId . "\n" . $apiTs . "\n" . $formUrl . "\n" . $formParams;
        $apiMac = hash_hmac('sha1', $signingString, $this->appSecret);
        return ('?api_mac=' . $apiMac . '&' . $formParams);
    }

    private function do_curl($formUrl, $getParams, $postData = false)
    {

        if ($postData) {

            $options = array(
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_HEADER => false,
                CURLOPT_URL => $this->urlRoot . $formUrl . $getParams,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $postData,
            );

        } else {

            $options = array(
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_HEADER => false,
                CURLOPT_URL => $this->urlRoot . $formUrl . $getParams,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
            );
        }

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        $header = curl_getinfo($feed);

        if (($error = curl_error($feed)) !== '') {
            curl_close($feed);

            throw new \Exception($error);
        }


        $response['http'] = $header['http_code'];
        $response['body'] = $json;

        return ($response);
    }
}