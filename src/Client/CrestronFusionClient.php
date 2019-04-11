<?php

namespace Brueggern\CrestronFusionHandler\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionClientException;

class CrestronFusionClient extends Client
{
    /** @var string baseUrl */
    protected $baseUrl;

    /** @var string auth token */
    protected $authToken;

    /** @var string auth user */
    protected $authUser;

    /**
     * Creat new Client
     *
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        parent::__construct();

        $this->baseUrl = $baseUrl;
    }

    /**
     * Set auth params
     *
     * @param string $authToken
     * @param string $authUser
     * @return void
     */
    public function setAuth(string $authToken, string $authUser)
    {
        $this->authToken = $authToken;
        $this->authUser = $authUser;
    }

    /**
     * Send a HTTP GET request to the Crestron Fusion API
     *
     * @param string $url
     * @param array $params
     * @return array
     */
    public function sendGETRequest(string $url, array $params) : array
    {
        if ($this->authToken && $this->authUser) {
            $auth = [
                'auth' => $this->authToken.' '.$this->authUser,
            ];
            $params = array_merge($auth, $params);
        }

        try {
            $response = $this->request('GET', $this->baseUrl.'/'.$url, [
                'query' => $params,
                'headers' => [
                    'Content-Type' => 'application/xml',
                ],
                'connect_timeout' => 10,
            ]);

            $xml = simplexml_load_string($response->getBody());
            return json_decode(json_encode($xml), true);
        }
        catch (ConnectException $e) {
            throw new CrestronFusionClientException($e->getMessage());
        }
    }

    public function sendPUTRequest(string $url, array $payload) : array
    {
        if ($this->authToken && $this->authUser) {
            $auth = [
                'auth' => $this->authToken.' '.$this->authUser,
            ];
            $payload = array_merge($auth, $payload);
        }

        try {
            $response = $this->request('PUT', $this->baseUrl.'/'.$url, [
                'query' => $payload,
                'headers' => [
                    'Content-Type' => 'application/xml',
                ],
                'connect_timeout' => 10,
            ]);

            $xml = simplexml_load_string($response->getBody());
            return json_decode(json_encode($xml), true);
        }
        catch (ConnectException $e) {
            throw new CrestronFusionClientException($e->getMessage());
        }
    }
}
