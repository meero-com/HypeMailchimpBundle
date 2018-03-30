<?php

namespace Hype\MailchimpBundle\Mailchimp;

use Buzz\Browser,
    Buzz\Client\Curl;

class RestClient
{

    protected $dataCenter;
    protected $listId;
    protected $config;

    /**
     * Constructor
     * @param array $config
     * @param string $listId
     * @param string $dataCenter
     */
    public function __construct($config, $listId, $dataCenter)
    {
        $this->config = $config;
        $this->listId = $listId;
        $this->dataCenter = $dataCenter;
    }

    /**
     * Prepare the curl request
     *
     * @param string $apiCall the API call function
     * @param array $payload Parameters
     * @param boolean $export indicate wether API used is Export API or not
     * @return string
     */
    protected function requestMonkey($apiCall, $payload, $export = false)
    {
        $payload['apikey'] = $this->config['api_key'];

        if ($export) {
            $url = $this->dataCenter . $apiCall;
        } else {
            $url = $this->dataCenter . '2.0/' . $apiCall;
        }
        $curl = $this->prepareCurl();
        $browser = new Browser($curl);
        $payload = json_encode($payload);
        $headers = array(
            "Accept" => "application/json",
            "Content-type" => "application/json"
        );
        $response = $browser->post($url, $headers, $payload);

        return $response->getBody()->getContents();
    }

    /**
     * resolve curl configuration
     * @return Curl
     */
    private function prepareCurl()
    {
        $options = array(
            CURLOPT_USERAGENT => 'HypeMailchimp',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->config['timeout']
        );
        $curl = new Curl($options);

        return $curl;
    }

}
