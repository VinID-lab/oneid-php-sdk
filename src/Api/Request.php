<?php


namespace OneId\Api;

use OneId\Utilities;

/**
 * Class Request - repesent a request to an API
 * @package OneId\Api
 */
class Request
{
    public $method;
    public $apiPath;
    public $body;
    public $nonce;
    public $timestamp;
    public $apiKey;
    public $signature;

    /**
     * Return JSON encoded body
     */
    public function getEncodedBody()
    {
//        return $this->body; // LongPV2 - fix bug JSON ENCODE twice
        return json_encode($this->body);
    }

    /**
     * Populate signature, then assign to $this->signature
     *
     * @param string $privateKey private key to make signature
     * @throws \OneId\InvalidPrivateKeyException
     */
    public function populateSignature($privateKey)
    {
        $this->signature = Utilities::generateSignature(
            $this->apiPath,
            $this->method,
            $this->nonce,
            $this->timestamp,
            $this->apiKey,
            $this->getEncodedBody(),
            $privateKey);
    }

    public function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Key-Code' => $this->apiKey,
            'X-Nonce' => $this->nonce,
            'X-Timestamp' => $this->timestamp,
            'X-Signature' => $this->signature,
        ];
    }

    public function getHeadersForCURL()
    {
        $headers = [];
        foreach ($this->getHeaders() as $key => $val) {
            array_push($headers, $key .': '. $val);
        }
        return $headers;
    }
}