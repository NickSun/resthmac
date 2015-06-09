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
        $string = $data['timestamp'] . $data['method'] . $data['host'] . $data['email'] . $data['image'];

        return base64_encode(hash_hmac('sha256', $string, $this->privateKey, true));
    }

    /**
     * Get the private key
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * See if two hashes match up
     *
     * @param string
     * @param string
     * @return bool
     */
    public static function isMatch($hash1, $hash2)
    {
        return $hash1 === $hash2;
    }
}


