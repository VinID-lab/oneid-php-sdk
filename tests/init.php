<?php

namespace OneId;


use OneId\Api\Client;
use OneId\Api\Response;
use PHPUnit\Framework\TestCase;

require_once(join(DIRECTORY_SEPARATOR, array(
    __DIR__,
    "..",
    "src",
    "autoload.php"
)));

const TEST_PRIVATE_KEY = <<<PKEY
-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEA6M9OQrNz6tC36ehKYiH+5/AUi7Vorf772OzFYebztnGEm0vi
PsD2Rs8nB1SibzdSOXN9NSLb81cmN01MaQO7m6jD4yyCjXcCgHcGBm5SpC2/3ZTv
C5dBF54BYcA/4bCsFkiyyMcaRUV64SE9LyGZR5YljZIHrs8AzPnMqmzb4z8i8VD8
5JirBC/0elEGNMiVkBCwwbOE8frOFGrgR10q4r7XXvV7SgeZGBKyZs9yJGc/+m7s
gkR/kSwukV6S+fyE/aYyJOdNtmaOjbG0tiWxpfpdOo5HZS7t2i4UgmvS44wRhs4s
WVRVWllHD1gavdqjCDY327yMebQelVQRuh0p3QIDAQABAoIBADiWu1UnPZPkK/Al
UH9N+CH0j2nuLIWupxTxaIEFnFPKgAsnhNTwHmzTyY7Uma8i6U+hrNuPn5skodtr
ZJlaGO4bNZIwrYMpXGhRhhtmEZxqqmp32yscFuxgscFK87wuL4YzIZIAI3iXDOlQ
JQkhx5pI+tPQnSF2m6E3TvJWB5TSu0FlwmIISjWQh/bxT11gPfxrr5ffoYQP7BnN
S+QYD1dF5JxRF3uHbUlP34tsA2NrDuFx0UGzoACrphy6uujXa/QP8MZxeKty2MS1
H3Re9Yiux/wtS/SQBBKpsiasDKuEci/Cpy5vJPzjUzO42BQN7QZciji8LBCDCYnQ
m5mPWAECgYEA9pdIUzvA5tPmPjXe+pa8K7DFfIf52FPci1jeZjVwLqQU3WmJEXkm
xopPNAf4YwuDgaauiaNlOOzRPJNDwODPMdpDd9WJaUP8zkrgEXdQxr+IaBFoRFR6
eKBiY753M+8q3H8rhZbhf/5fEog2vkbg+4X/uT11PmJ16ICueok6cQECgYEA8bFo
acRw2SWKg9jh7AMa2t40zUEInxdkzoQ+kqrZ/9K3SNQQ+f0fCJo3L6MlX2TENW6k
WSv+18CMVTRgcW0ffdRKD/2hFJxlgUXHD5FEB3qbus9kK3fgcr1yhPOuX0JFSB43
er3Y46ETHNsEPrhhnKP3rrZYdI1XkcOeNSjNnN0CgYEAlvXMcDAfivBkfudJ540K
C5E/hVpVKQtF9ATmuhmy4MrQfy/RmuHZTCh2Dntmo3P0ARZCub5PBIduHLBnBRhb
n8BoF1+hrNDTXpNWEztBNzsgTd2CQHqbM2e0dC2xGhkr2yr7QOA6krnuCBFduiT4
LOM+x2+JbDSozDqjFh8WqQECgYEAiBU/mO3GLD169CmVavGEmV8rk7XeSNU/KRhE
swgHnobiM9tTg64FXy6Vi/jr/f4ai4s0dhDTeF8tpHvNIZAzfwGcgcxoedZlQJgt
MJK2Hw/lxxUmbWMduPz6EemycGg74hNBYZarG9+Bh2m5xibCrxOTTYfV3ioG+EeQ
OJY0zvECgYEA0+44q/vHNsko0O1FvNSeB+4JPZDCHr7rlwRrw1+ijDyX7qvuGo0f
AhNdmuNwWZg8emFFEKD/3zeymSvLN47q6BnKVPbQv4TukCspMgUi6RNeZQRQ0lkz
NDYYB1O4E5bCJHazBSz5jsc/UyxbTwax1vVGr2aXIgsbLVAoQoZzznM=
-----END RSA PRIVATE KEY-----
PKEY;

const TEST_PUBLIC_KEY = <<<PKEY
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArhgQ/t9K9zZ/gG+GxOfS
1nsUYWOm5s+giLrVlGMsN2zQVVWu92qfvYT8jqdpAdpzXUnvBjX/iY13/NUTG8gA
b0aXcOBJUtxiTScWjQ69a/FrRPyAIdGE3okLE3Gn1MF+FsegCYoHfg42NBzhvPxB
S8bmAHe8NBJS/PnY3MFD4ZxxtoDtESAeYwiJHmFZiCVyizAvIDE3wZ2LWg+eq6cW
k1gUnjL8L1bJiX1U5Sl5eFnKXSBg2EqjSJXqYJzDZ+m3zDizrcU+lfDdHwXfnOzg
sfdgRroipkAZSCoGOyxtSM2ASiGiYjVfE+Lf0iv4n3FvcDDdUdEcl2pw6chHYxZE
zwIDAQAB
-----END PUBLIC KEY-----
PKEY;

const TEST_API_KEY = "28f04867-c6d5-425f-9108-0d90a0c0b09e";


class ApiTestCases extends TestCase
{
    /**
     * Return a mocked object that represent an API call with specific response data
     * @param $responseData
     * @return Client
     */
    function createMockClient($responseData, $code = 200, $msg = "OK", $httpCode = 200, $httpStatus = "OK")
    {
        $body = [
            "meta" => [
                "code" => $code,
                "message" => $msg,
            ],
            "data" => $responseData,
        ];
        $body = json_encode($body);
        $headers = sprintf("HTTP/1.1 %d %s", $httpCode, $httpStatus);
        $response = new Response($body, $headers);
        $mock = $this->createMock(Client::class);
        $mock->method("request")->willReturn($response);
        return $mock;
    }
}