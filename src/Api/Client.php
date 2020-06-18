<?php

namespace OneId\Api;

use OneId\Api\NonceManager\iNonceManager;
use OneId\Api\NonceManager\RandomNonceManager;
use OneId\Utilities;
use OneId\Url;

class Client
{
    /**
     * @var Client
     */
    static private $_defaultClient;

    private $baseUrl;

    private $apiKey;
    private $privateKey;
    private $nonceManager;

    public function __construct($apiKey=null, $privateKey=null, $baseUrl=null, $nonceManager=null)
    {
        $this->setNonceManager($nonceManager);
        $this->setApiKey($apiKey);
        $this->setPrivateKey($privateKey);
        $this->setBaseUrl($baseUrl);
    }

    public function getApiKey()
    {
        if (is_null($this->apiKey)) {
            $this->apiKey = Utilities::readValueFromEnv("ONEID_API_KEY");
        }
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getPrivateKey()
    {
        if (is_null($this->privateKey)) {
            $this->privateKey = Utilities::readValueFromEnv("ONEID_PRIVATE_KEY");
        }
        return $this->privateKey;
    }

    public function setNonceManager($manager)
    {
        if (is_null($manager)) return;
        if (!($manager instanceof iNonceManager)) {
            trigger_error(sprintf("%s is not instance of %s\iNonceManager", $manager, __NAMESPACE__), E_USER_ERROR);
            return;
        }
        $this->nonceManager = $manager;
    }

    /**
     * @return iNonceManager
     */
    public function getNonceManager()
    {
        if (is_null($this->nonceManager)) {
            $this->nonceManager = new RandomNonceManager();
        }
        return $this->nonceManager;
    }

    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    public function getBaseUrl()
    {
        if (is_null($this->baseUrl)) {
            $baseUrl = Utilities::readValueFromEnv("ONEID_API_BASEURL");
            if (is_null($baseUrl)) {
                $oneIdEnv = strtolower(Utilities::readValueFromEnv("ONEID_ENV", "sandbox"));
                if ($oneIdEnv == "prod" || $oneIdEnv == "production") {
                    $baseUrl = Url::API_BASEURL_PRODUCTION;
                } else {
                    $baseUrl = Url::API_BASEURL_SANDBOX;
                }
            }
            $this->baseUrl = $baseUrl;
        }
        return $this->baseUrl;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getApiEndPoint($apiPath)
    {
        return $this->getBaseUrl().$apiPath;
    }

    /**
     * Prepare for a curl handle for an API request
     * @param $method
     * @param $url
     * @param $body
     * @param null $nonce
     * @param null $timestamp
     * @return Request
     * @throws \OneId\InvalidPrivateKeyException
     */
    public function prepareRequest($method, $url, $body, $nonce=null, $timestamp=null)
    {
        $req = new Request();
        if (is_null($nonce)) $nonce = $this->getNonceManager()->generateNonce();
        if (is_null($timestamp)) $timestamp = time();

        $req->method = $method;
        $req->apiPath = $url;
        $req->body = $body;
        $req->nonce = $nonce;
        $req->timestamp = $timestamp;
        $req->apiKey = $this->getApiKey();
        $req->populateSignature($this->getPrivateKey());

        return $req;
    }

    /**
     * Make request to OneID's APIs
     *
     * @param string $method
     * @param string $url
     * @param mixed $body
     * @param string|null $nonce
     * @param string|null $timestamp
     * @return bool|string
     * @throws \OneId\InvalidPrivateKeyException
     * @return \OneId\Api\Response
     */
    public function request($method, $url, $body, $nonce=null, $timestamp=null)
    {
        $req = $this->prepareRequest($method, $url, $body, $nonce, $timestamp);
        $curl = curl_init($this->getApiEndPoint($url));
        /* print_r(PHP_EOL); */
        /* print_r("REQUEST".PHP_EOL); */
        /* print_r($req->getEncodedBody().PHP_EOL); */
//        curl_setopt($ch, CURLOPT_URL, $this->getApiEndPoint($url));
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
        } else {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($curl, CURLOPT_HEADER, true); // we want to get response headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, $req->getHeadersForCURL());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $req->getEncodedBody());
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $responseText = curl_exec($curl);
        
        if ($responseText === false) {
            trigger_error(curl_error($curl), E_USER_WARNING);
            return null;
        }

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $resHeaders = substr($responseText, 0, $headerSize);
        $resBody = substr($responseText, $headerSize);
        curl_close($curl);

        return new Response($resBody, $resHeaders);
    }

    /**
     * @return Client
     */
    static function defaultClient()
    {
        if (is_null(self::$_defaultClient))
            self::$_defaultClient = new Client();

        return self::$_defaultClient;
    }
}
