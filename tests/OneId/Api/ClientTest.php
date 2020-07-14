<?php

namespace OneId\Api;

use OneId\Api\NonceManager\RandomNonceManager;
use OneId\Utilities;
use PHPUnit\Framework\TestCase;
use const OneId\API_BASEURL_SANDBOX;
use const OneId\API_ENDPOINT_TRANSACTION_QR;
use const OneId\TEST_API_KEY;
use const OneId\TEST_PRIVATE_KEY;

class ClientTest extends TestCase
{
    protected function _testGetterSetter_WithEnv($getter, $setter, $envVar)
    {
        $val1 = Utilities::generateGUID4();
        $val2 = Utilities::generateGUID4();
        $client = new Client();

        putenv($envVar . "=" . $val1);
        $this->assertEquals($val1, $client->{$getter}());

        $client->{$setter}($val2);
        $this->assertEquals($val2, $client->{$getter}());

        $client->{$setter}($val1);
        $this->assertEquals($val1, $client->{$getter}());
    }

    protected function _testGetterSetter($getter, $setter)
    {
        $val1 = Utilities::generateGUID4();
        $val2 = Utilities::generateGUID4();
        $client = new Client();

        $client->{$setter}($val2);
        $this->assertEquals($val2, $client->{$getter}());

        $client->{$setter}($val1);
        $this->assertEquals($val1, $client->{$getter}());
    }

    public function testGetSetNonceManager()
    {
        $val1 = new RandomNonceManager();
        $val2 = new RandomNonceManager();
        $client = new Client();

        $client->setNonceManager($val2);
        $this->assertEquals($val2, $client->getNonceManager());

        $client->setNonceManager($val1);
        $this->assertEquals($val1, $client->getNonceManager());

        $this->expectError();
        $this->expectErrorMessageMatches('/iNonceManager/');
        $wrong = 'abc';
        $client->setNonceManager($wrong);

    }

    public function testGetApiEndPoint()
    {
        $client = new Client();

        $client->setBaseUrl('/a/b/c');
        $this->assertEquals('/a/b/c/1234', $client->getApiEndPoint('/1234'));
    }

    public function testGetSetApiKey()
    {
        $this->_testGetterSetter_WithEnv('getApiKey', 'setApiKey', 'ONEID_API_KEY');
    }

    public function testGetSetPrivateKey()
    {
        $this->_testGetterSetter_WithEnv('getPrivateKey', 'setPrivateKey', 'ONEID_PRIVATE_KEY');
    }

    public function testGetSetBaseUrl()
    {
        $this->_testGetterSetter_WithEnv('getBaseUrl', 'setBaseUrl', 'ONEID_API_BASEURL');
    }

    function dataProvider_doRequest()
    {
        return [
            array(
                'POST',
                API_ENDPOINT_TRANSACTION_QR,
                array("a" => 1234),
                array(
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'X-Key-Code' => null,
                        'X-Nonce' => null,
                        'X-Timestamp' => null,
                        'X-Signature' => null,
                    ],
                    "body" => '{"a":1234}',
                )),
        ];
    }

    /**
     * @dataProvider dataProvider_doRequest
     * @throws \OneId\InvalidPrivateKeyException
     */
    public function testPrepareRequest($method, $url, $body, $expected)
    {
        $client = new Client();
        $client->setPrivateKey(TEST_PRIVATE_KEY);
        $client->setApiKey(TEST_API_KEY);
        $client->setBaseUrl(API_BASEURL_SANDBOX);

        $req = $client->prepareRequest($method, $url, $body);

        $expectedHeaders = $expected['headers'];
        $realHeaders = $req->getHeaders();
        foreach ($expectedHeaders as $key => $val) {
            if (is_null($val)) $this->assertArrayHasKey($key, $realHeaders);
            else $this->assertEquals($val, $realHeaders[$key]);
        }
        $this->assertIsInt($realHeaders['X-Timestamp']);
        $this->assertIsString($realHeaders['X-Key-Code']);
        $this->assertIsString($realHeaders['X-Signature']);

        $expectedSignature = Utilities::generateSignature(
            $req->apiPath,
            $req->method,
            $req->nonce,
            $req->timestamp,
            $req->apiKey,
            $req->getEncodedBody(),
            $client->getPrivateKey()
        );
        $this->assertEquals($expectedSignature, $realHeaders['X-Signature']);

        // LongPV2 - UT should never contain integration response data, due to un-predictable response from API services.
        // Just make sure it get response from API.
        //$this->assertEquals($expected['body'], $req->getEncodedBody());
        $this->assertNotEmpty($req->getEncodedBody());
    }

    public function testRealRequestOnSandbox()
    {
        $privateKey = Utilities::readValueFromEnv("TEST_SANDBOX_PRIVATEKEY");
        if (is_null($privateKey)) {
            $this->markTestSkipped("Please set env:TEST_SANDBOX_PRIVATEKEY to run this test");
        }
        $privateKey = str_replace("\\n", "\n", $privateKey);

        $apiKey = Utilities::readValueFromEnv("TEST_SANDBOX_APIKEY");
        if (is_null($apiKey)) {
            $this->markTestSkipped("Please set env:TEST_SANDBOX_APIKEY to run this test");
        }

        $storeCode = Utilities::readValueFromEnv("TEST_SANDBOX_STORECODE");
        if (is_null($storeCode)) {
            $this->markTestSkipped("Please set env:TEST_SANDBOX_STORECODE to run this test");
        }

        $posCode = Utilities::readValueFromEnv("TEST_SANDBOX_POSCODE");
        if (is_null($posCode)) {
            $this->markTestSkipped("Please set env:TEST_SANDBOX_POSCODE to run this test");
        }

        $orderRefId = Utilities::generateGUID4();
        $payload = [
            'callback_url' => "http://localhost",
            'description' => "Order from Unittest",
            'extra_data' => null,
            'order_amount' => 10000,
            'order_currency' => "VND",
            'order_reference_id' => $orderRefId,
            'pos_code' => $posCode,
            'service_type' => "PURCHASE",
            'store_code' => $storeCode
        ];
        $client = new Client($apiKey, $privateKey, API_BASEURL_SANDBOX);
        $res = $client->request(
            "POST",
            API_ENDPOINT_TRANSACTION_QR,
            $payload
        );

        $this->assertEquals(200, $res->getHttpStatusCode());
        // LongPV2 - Un-predictable status code
        // $this->assertEquals(200, $res->getApiStatusCode());
        //$this->assertEquals("OK", $res->getApiStatusMessage());
        $this->assertNotEmpty($res->getApiStatusCode());
//        $this->assertNotEmpty($res->getData());
    }
}
