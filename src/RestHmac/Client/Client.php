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
        $this->client = new \Guzzle\Http\Client(['base_uri' => $apiEndPoint]);
        $this->hmac = new HmacAuthenticate($privateKey);
        $this->headers['X-Client-Id'] = $publicKey;
    }

    public function registerUser($data)
    {
        $timestamp = time();
        $this->headers['X-Timestamp'] = $timestamp;
        $data['timestamp'] = $timestamp;
        $data['method'] = 'POST';
        $data['host'] = $_SERVER['SERVER_NAME'];

        $this->headers['X-Hash'] = $this->hmac->generate($data);

        $res = $this->client->post('user', $this->headers, $data);

        return $res;
    }
}
