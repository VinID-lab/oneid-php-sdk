<?php

use OneId\Utilities;

use PHPUnit\Framework\TestCase;
use const OneId\TEST_PRIVATE_KEY;

class UtilitiesTest extends TestCase
{
    function generateSignatureDataProvider()
    {
        return [
            'succeed 01' => [
                "https://a.b",
                "POST",
                "12345678",
                123456789,
                "123456789@123abc",
                '{"a":2,"b":1}',
                TEST_PRIVATE_KEY,
                "VR8hbfpXbrC7xmRytlC1sjiAvp2fvwDKAxgVynmobB14vrHD9ONnhjGqXLd1zF7vzD7WltgklQ0hfEyhB4jxW/G0zwBQMqvlHsWBU4cBwugMg84D5kA025op+nqlLYWR4Y5Pw/cLJtE614EGRpsH8MIeXBOXR9qb5tt7xrWAWqrAM0hIxM9flWF4D0s0jdZqN6ksuTlMgiNhDPXaeZb0lJRklDmf2N4+V9m0UnNUdlzwA76iG9YHjLSbhVrFnJLmYLVkXWn+rU99LeiX/rLnhVpWJqkbrTPT8qto8RMSAvOf2tKnP0/q5T/3+Izrcqpqq2ZhjQv2ecmDML1pTS7ZOw=="
            ],
            'succeed 02' => [
                "https://a.b",
                "POST",
                "12345678",
                123456789,
                "123456789@123abd",
                '{"a":2,"b":1}',
                TEST_PRIVATE_KEY,
                "vvoTTd2Xmu65l8CQ6O81Scnjl/bVPU/VwGP7PqmYbIKRABeUC47s3tf8fRRAmQB+IjiWkZ+CHpX2jVSVpMB81DJBfe03/jUC1gHUAsCuE73+nPpsNesOIBrtcirFwRza0x946lHIePw8AQmiVkn/LlPyI+TeeI+WonOukI4kY3aWWVj3tZVraRDTYfi8Hn18ocZKdib34Icuw8rw+aPBSflUR2qMN4EDUnwLW5/ktXegxV7FbfBoCMKqizRwGopzQXMx/hLCc+FWFSoNFzsnIjOYBdIf/w25Essmb/LRTz5elzysnmftlJ+C6Em35EMz9mQopIi5gNrfmb6+Q0+qmw=="
            ],
        ];
    }

    /**
     * @dataProvider generateSignatureDataProvider
     */
    public function testGenerateSignature_Succeed($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey, $expected)
    {
        $signature = Utilities::generateSignature($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey);
        $this->assertEquals($expected, $signature);
    }

    /**
     * @dataProvider generateSignatureDataProvider
     * @expectedException \OneId\InvalidPrivateKeyException
     */
    public function testGenerateSignature_WrongKey($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey, $expected)
    {
        $charPosToRemove = random_int(0, strlen($privateKey));
        $wrongPrivateKey = substr($privateKey, 0, $charPosToRemove) . substr($privateKey, $charPosToRemove+1);
        $this->expectException("OneId\InvalidPrivateKeyException");
        $this->expectWarning();
        $signature = Utilities::generateSignature($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $wrongPrivateKey);
    }
}
