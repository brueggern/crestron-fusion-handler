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
    public function getRequest(string $url, array $params) : array
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
                    'Content-Type' => 'application/json',
                ],
                'connect_timeout' => 10,
            ]);

            return json_decode($response->getBody(), true);
        }
        catch (ConnectException $e) {
            throw new CrestronFusionClientException($e->getMessage());
        }
    }
}
