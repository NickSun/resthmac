<?php
namespace RestHmac\Client;

use RestHmac\HmacAuthenticate\HmacAuthenticate;
use RestHmac\Request\Request;

/**
 * Class Client
 * @package RestHmac\Client
 */
class Client
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var HmacAuthenticate */
    protected $hmac;

    /** @var array */
    protected $headers;

    /** @var Request */
    protected $request;

    /**
     * @param $apiEndPoint
     * @param $publicKey
     * @param $privateKey
     */
    public function __construct($apiEndPoint, $publicKey, $privateKey)
    {
        $this->client = new \GuzzleHttp\Client(['base_uri' => $apiEndPoint]);
        $this->hmac = new HmacAuthenticate($privateKey);
        $this->request = new Request();
        $this->headers['X-Client-Id'] = $publicKey;
    }

    /**
     * @param $postData
     * @return bool
     */
    public function registerUser($postData)
    {
        $data = $postData;
        $timestamp = time();
        $this->headers['X-Timestamp'] = $timestamp;
        $data['timestamp'] = $timestamp;
        $data['method'] = 'POST';

        $this->headers['X-Hash'] = $this->hmac->generate($data);

        $postData = $this->request->encodeData($postData);

        $response = $this->client->post('user', ['headers'  => $this->headers, 'form_params' => $postData]);

        if (202 == $response->getStatusCode()) {
            return true;
        }

        return false;
    }
}
