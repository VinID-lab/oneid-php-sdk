<?php

namespace OneId;


use Throwable;

class InvalidPrivateKeyException extends \Exception
{
    public $privateKey;

    public function __construct($privateKey)
    {
        $this->privateKey = $privateKey;
        parent::__construct("Can not load private key");
    }
}

class InvalidParamsException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class Utilities
{
    /**
     * @todo -o Long please comment here
     * @param string $url
     * @param string $method POST or GET
     * @param string $nonce
     * @param int $timestamp
     * @param string $apiKey
     * @param string $requestBody
     * @param string $privateKey PEM formation for RSA private key
     * @return string the generated signature. Empty string if can not generate signature
     * @throws InvalidPrivateKeyException
     */
    static function generateSignature($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey)
    {
        $data = $url . ";" . $method . ";" . $nonce . ";" . $timestamp . ";" . $apiKey . ";" . $requestBody;
        $p = openssl_pkey_get_private($privateKey);
        if (!$p) {
            trigger_error("Invalid private key\n".$privateKey, E_USER_WARNING);
            throw new InvalidPrivateKeyException($privateKey);
        }
        $signSuccess = openssl_sign($data, $signature, $p, OPENSSL_ALGO_SHA256);
        $encodedSignature = base64_encode($signature);
        openssl_free_key($p);
        return $encodedSignature;
    }

    /**
     * Read value from ENV
     *
     * @param string $envVar env var's name
     *
     * @return string env's value or null if there is not var
     */
    static function readValueFromEnv($envVar, $defaultValue=null)
    {
        $value = getenv($envVar);
        if ($value === false) return $defaultValue;
        return $value;
    }

    /**
     * Generate GUID version 4 (Random)
     * @return string
     */
    static function generateGUID4()
    {
//        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        return sprintf( '%04x%04x%04x%04x%04x%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}
