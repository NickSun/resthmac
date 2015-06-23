<?php
namespace RestHmac\Server;

use RestHmac\HmacAuthenticate\HmacAuthenticate;
use RestHmac\Request\Request;

/**
 * Class Server
 * @package RestHmac\Server
 */
class Server
{
    const LAG = 180;

    /** @var HmacAuthenticate */
    protected $hmac;

    /** @var Request */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->hmac = new HmacAuthenticate($privateKey);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function checkAccess()
    {
        if (null === $this->hmac) {
            throw new \Exception('Private key does not set.');
        }

        $data = $this->request->getData();

        if (time() - $data['timestamp'] > self::LAG) {
            return false;
        }

        $hash = $this->hmac->generate($data);

        return $hash === $data['hash'];
    }
}