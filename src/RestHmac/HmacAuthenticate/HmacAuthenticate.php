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
        $string = $this->getDataAsString($data);

        return base64_encode(hash_hmac('sha256', $string, $this->privateKey, true));
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getDataAsString(array $data)
    {
        $string = '';

        ksort($data);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->getDataAsString($value);
            } else {
                $string .= $value;
            }
        }

        return $string;
    }
}