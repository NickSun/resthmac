<?php
namespace RestHmac\Request;

/**
 * Class Request
 * @package RestHmac\Request
 */
class Request
{
    /** @var array Headers */
    protected $headers = [];

    /**
     * @param string $header
     *
     * @return bool
     */
    public function getHeader($header)
    {
        if (!$this->headers) {
            $this->parseHeader();
        }

        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = [];

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $data = $_POST;
                break;
            case 'GET':
                $data = $_GET;
                break;
            case 'PUT':
            case 'DELETE':
                $putdata = file_get_contents('php://input');
                $exploded = explode('&', $putdata);

                foreach($exploded as $pair) {
                    $item = explode('=', $pair);
                    if(count($item) == 2) {
                        $data[urldecode($item[0])] = urldecode($item[1]);
                    }
                }

                break;
            default:
                $data = [];
                break;
        }

        $data['timestamp'] = $this->getHeader('X-Timestamp');
        $data['hash'] = $this->getHeader('X-Hash');

        return $data;
    }

    /**
     * @return $this
     */
    protected function parseHeader()
    {
        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $this->headers = $headers;

        return $this;
    }
}