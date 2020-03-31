<?php

namespace OneId\Api;
# Forked from https://github.com/anlutro/php-curl/blob/master/src/Response.php

/**
 * Class Response - represent a reponse for an API
 * @package OneId\Api
 */
class Response
{
    /**
     * The response headers.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $rawHeaders;

    /**
     * The response body.
     *
     * @var mixed
     */
    protected $body;

    /**
     * @var string
     */
    protected $rawBody;

    /**
     * @var mixed
     */
    protected $meta;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * The response code including text, e.g. '200 OK'.
     *
     * @var string
     */
    protected $httpStatusText;

    /**
     * The response code.
     * @var int
     */
    protected $httpStatusCode;

    /**
     * @param string $body
     * @param string $headers
     */
    public function __construct($body, $headers)
    {
        $this->rawHeaders = $headers;
        $this->rawBody = $body;

        $this->parseHeader($headers);

        $this->body = json_decode($body, true);
        $this->meta = $this->body['meta'];
        $this->data = $this->body['data'];
    }

    /**
     * Parse a header string.
     *
     * @param string $header
     *
     * @return void
     */
    protected function parseHeader($header)
    {
        $headers = explode("\n", trim($header));
        $this->parseHeaders($headers);
    }

    /**
     * Parse an array of headers.
     *
     * @param array $headers
     *
     * @return void
     */
    protected function parseHeaders(array $headers)
    {
        $this->headers = array();

        // find and set the HTTP status code and reason
        $firstHeader = array_shift($headers);
        if (!preg_match('/^HTTP\/\d(\.\d)? [0-9]{3}/', $firstHeader)) {
            throw new \InvalidArgumentException('Invalid response header');
        }
        list(, $status) = explode(' ', $firstHeader, 2);
        $code = explode(' ', $status);
        $code = (int)$code[0];

        // special handling for HTTP 100 responses
        if ($code === 100) {
            // remove empty header lines between 100 and actual HTTP status
            foreach ($headers as $key => $header) {
                if ($header) {
                    break;
                }
                unset($headers[$key]);
            }

            // start the process over with the 100 continue header stripped away
            return $this->parseHeaders($headers);
        }

        $this->httpStatusText = $status;
        $this->httpStatusCode = $code;

        foreach ($headers as $header) {
            // skip empty lines
            if (!$header) {
                continue;
            }

            $delimiter = strpos($header, ':');
            if (!$delimiter) {
                continue;
            }

            $key = trim(strtolower(substr($header, 0, $delimiter)));
            $val = ltrim(substr($header, $delimiter + 1));

            if (isset($this->headers[$key])) {
                if (is_array($this->headers[$key])) {
                    $this->headers[$key][] = $val;
                } else {
                    $this->headers[$key] = array($this->headers[$key], $val);
                }
            } else {
                $this->headers[$key] = $val;
            }
        }
    }

    /**
     * Get a specific header from the response.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getHeader($key)
    {
        $key = strtolower($key);

        return array_key_exists($key, $this->headers) ?
            $this->headers[$key] : null;
    }

    /**
     * Gets all the headers of the response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Convert the object to its string representation by returning the body.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->rawHeaders . "\n\n" . $this->rawBody;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getApiStatusCode()
    {
        return $this->meta['code'];
    }

    public function getApiStatusMessage()
    {
        return $this->meta['message'];
    }

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    public function getHttpStatusText()
    {
        return $this->httpStatusText;
    }
}