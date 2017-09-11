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
    private $client;
    /** @var array */
    private $headers;
    /** @var HmacAuthenticate */
    private $hmac;
    /** @var Request */
    private $request;

    /**
     * @param string $apiEndPoint
     * @param string $publicKey
     * @param string $privateKey
     * @throws \Exception
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
        $data['clientId'] = $this->headers['X-Client-Id'];

        $this->headers['X-Hash'] = $this->hmac->generate($data);

        $postData = $this->request->encodeData($postData);

        $response = $this->client->post('user', ['headers'  => $this->headers, 'form_params' => $postData]);

        return 202 === $response->getStatusCode();
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getStatistic()
    {
        $timestamp = time();
        $this->headers['X-Timestamp'] = $timestamp;
        $data['timestamp'] = $timestamp;
        $data['method'] = 'GET';
        $data['clientId'] = $this->headers['X-Client-Id'];

        $this->headers['X-Hash'] = $this->hmac->generate($data);

        $response = $this->client->get('statistic', ['headers'  => $this->headers]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(
                'Expected status code 200, got ' . $response->getStatusCode() . PHP_EOL .
                $response->getBody()->getContents()
            );
        }

        return $response;
    }

    /**
     * @param array $data
     * @throws \RuntimeException
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get(array $data)
    {
        $this->addHashHeader($data, 'GET');
        $response = $this->client->get('?' . http_build_query($data), ['headers' => $this->headers]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(
                'Expected status code 200, got ' . $response->getStatusCode() . PHP_EOL .
                $response->getBody()->getContents()
            );
        }

        return $response;
    }

    /**
     * @param array  $data
     * @param string $method
     */
    private function addHashHeader(array $data, $method)
    {
        $timestamp = time();
        $this->headers['X-Timestamp'] = $timestamp;
        $this->headers['X-Hash'] = $this->hmac->generate(
            array_merge(
                $data,
                [
                    'timestamp' => $timestamp,
                    'method'    => $method,
                    'clientId'  => $this->headers['X-Client-Id']
                ]
            )
        );
    }

    /**
     * @param array $data
     * @throws \RuntimeException
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post(array $data)
    {
        $this->addHashHeader($data, 'POST');
        $response = $this->client->post('', [
            'headers'     => $this->headers,
            'form_params' => $this->request->encodeData($data)
        ]);

        if ('2' !== substr($response->getStatusCode(), 0, 1)) {
            throw new \RuntimeException(
                'Unexpected status code ' . $response->getStatusCode() . PHP_EOL .
                $response->getBody()->getContents()
            );
        }

        return $response;
    }
}
