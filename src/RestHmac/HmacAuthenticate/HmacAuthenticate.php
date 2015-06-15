<?php
namespace RestHmac\HmacAuthenticate;

/**
 * Class HmacAuthenticate
 * @package RestHmac\HmacAuthenticate
 */
class HmacAuthenticate
{
    /** @var string Shared secret key used for generating the HMAC variant of the message digest */
    protected $privateKey;

    /**
     * Constructor
     *
     * @param string $privateKey Shared secret key used for generating the HMAC variant of the message digest
     *
     * @throws \Exception
     */
    public function __construct($privateKey)
    {

        if (!empty($privateKey)) {
            $this->privateKey = $privateKey;
        } else {
            throw new \Exception('Secret key can not be empty.');
        }
    }

    /**
     * Generate an encoded hash
     *
     * @param array $data
     *
     * @return string
     */
    public function generate(array $data)
    {
        // Message to be hashed
        $string = $data['timestamp'] .
            $data['method'] .
            $data['firstname'] .
            $data['lastname'] .
            $data['email'] .
            implode(',', $data['images']);

        return base64_encode(hash_hmac('sha256', $string, $this->privateKey, true));
    }
}