<?php

namespace Brueggern\CrestronFusionHandler\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionClientException;

class CrestronFusionClient extends Client
{
    /** @var string auth token */
    protected $authToken;

    /** @var string auth user */
    protected $authUser;

    /**
     * An auth token and an auth user must be set
     *
     * @param string $authToken
     * @param string $authUser
     */
    public function __construct(string $authToken, string $authUser)
    {
        parent::__construct();

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
        $auth = [
            'auth' => $this->authToken.' '.$this->authUser,
        ];

        try {
            $response = $this->request('GET', $url, [
                'query' => array_merge($auth, $params),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'connect_timeout' => 10,
            ]);

            return json_decode($response->getBody(), true);
        }
        catch (ConnectException $e) {
            throw new CrestronFusionClientException();
        }
    }
}
