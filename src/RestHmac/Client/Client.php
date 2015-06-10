<?php
namespace RestHmac\Client;

use RestHmac\HmacAuthenticate\HmacAuthenticate;

/**
 * Class Client
 * @package RestHmac\Client
 */
class Client
{
    protected $client;
    protected $headers;

    public function __construct($apiEndPoint, $publicKey, $privateKey)
    {
        $this->client = new \GuzzleHttp\Client($apiEndPoint);
        $this->hmac = new HmacAuthenticate($privateKey);
        $this->headers['X-Client-Id'] = $publicKey;
    }

    public function registerUser($postData)
    {
        $data = $postData;
        $timestamp = time();
        $this->headers['X-Timestamp'] = $timestamp;
        $data['timestamp'] = $timestamp;
        $data['method'] = 'POST';
        $data['host'] = $_SERVER['SERVER_NAME'];

        $this->headers['X-Hash'] = $this->hmac->generate($data);

        $response = $this->client->post('user', $this->headers, $postData);

        if (202 == $response->getStatusCode()) {
            return true;
        }

        return false;
    }
}
