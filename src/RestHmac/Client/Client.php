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
        $this->client = new \GuzzleHttp\Client(['base_uri' => $apiEndPoint]);
        $this->hmac = new HmacAuthenticate($privateKey);
        $this->headers['X-Client-Id'] = $publicKey;
    }

    public function registerUser($postData)
    {
        $data = $postData;
        $timestamp = time();
        $this->headers['X-Timestamp'] = $timestamp;
        $data['timestamp'] = $timestamp;

        $this->headers['X-Hash'] = $this->hmac->generate($data);

        $response = $this->client->post('user', ['headers'  => $this->headers, 'form_params' => $postData]);

        if (202 == $response->getStatusCode()) {
            return true;
        }

        return false;
    }
}
