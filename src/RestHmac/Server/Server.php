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
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setPrivateKey($privateKey)
    {
        $this->hmac = new HmacAuthenticate($privateKey);
    }

    public function checkAccess()
    {
        if (is_null($this->hmac)) {
            throw new \Exception('Private key does not set.');
        }

        $data = $this->request->getData();

        if (time() - $data['timestamp'] > self::LAG) {
            return false;
        }

        $hash = $this->hmac->generate($data);

        if ($hash !== $this->request->getHeader('X-Hash')) {
            return false;
        }

        return true;
    }
}