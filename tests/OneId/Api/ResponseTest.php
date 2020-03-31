<?php

namespace OneId\Api;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    static protected $strHeaders = <<<HEADERS
HTTP/1.1 200 OKAhihi
Date: Mon, 02 Mar 2020 04:15:43 GMT
Content-Type: application/json; charset=utf-8
Transfer-Encoding: chunked
Connection: close
Set-Cookie: __cfduid=d17ef257bc41217c6cc999b06d6f8b8f21583122543; expires=Wed, 01-Apr-20 04:15:43 GMT; path=/; domain=.vinid.dev; HttpOnly; SameSite=Lax; Secure
X-Request-ID: eb79bbea-77cd-455a-82ff-a33041671914#1098
CF-Cache-Status: DYNAMIC
Expect-CT: max-age=604800, report-uri="https://report-uri.cloudflare.com/cdn-cgi/beacon/expect-ct"
Alt-Svc: h3-27=":443"; ma=86400, h3-25=":443"; ma=86400, h3-24=":443"; ma=86400, h3-23=":443"; ma=86400
Server: cloudflare
CF-RAY: 56d84c563ceea366-HKG
Content-Encoding: gzip
HEADERS;
    
    static protected $expectedHeaders = [
        "date" => "Mon, 02 Mar 2020 04:15:43 GMT",
        "content-type" => "application/json; charset=utf-8",
        "transfer-encoding" => "chunked",
        "connection" => "close",
        "set-cookie" => "__cfduid=d17ef257bc41217c6cc999b06d6f8b8f21583122543; expires=Wed, 01-Apr-20 04:15:43 GMT; path=/; domain=.vinid.dev; HttpOnly; SameSite=Lax; Secure",
        "x-request-id" => "eb79bbea-77cd-455a-82ff-a33041671914#1098",
        "cf-cache-status" => "DYNAMIC",
        "expect-ct" => "max-age=604800, report-uri=\"https://report-uri.cloudflare.com/cdn-cgi/beacon/expect-ct\"",
        "alt-svc" => "h3-27=\":443\"; ma=86400, h3-25=\":443\"; ma=86400, h3-24=\":443\"; ma=86400, h3-23=\":443\"; ma=86400",
        "server" => "cloudflare",
        "cf-ray" => "56d84c563ceea366-HKG",
        "content-encoding" => "gzip",
    ];

    static protected $strBody = <<<BODY
{
  "meta": {
    "code": 2001,
    "message": "OK Done"
  },
  "data": {
    "qr_url": "",
    "qr_data": "iVBORw0KGgoAAAANSUhEUgAAAQAAAAEAAQMAAABmvDolAAAABlBMVEX///8AAABVwtN+AAACTklEQVR42uyYPW7kOhCES1DAkEfgUXgxQxLhi/EoPAJDBoTqoVp6nrF3Nx5ydzowYOILpqGq/sM73vFPxkKSp2MHXF4LfG6BNeo1TwQ0AJvL8KfTa40Ne40A/FRAYN0cu08kQiJbSDXqYTRAP/pA7OG4ZDQr0KWtjp2cEzBFkRUAy4Y/SO61wGXe3H1qcS2bzJt+4+6xgSuU5kKuPL3MUX8pp8MD5u4Mn5hNUQ3wbNi/0nw9sLCsdWlYVYdMUfoMBD6+etYMQAumKH2LfPv2kDk+ntw9PiAdJWWWGrp5Wr5AOGYC4Mpq7lah6pexk1XToQBKUdF8AXNDILMqPyYCELrMvFYb1fZ7iED4fNh/fEBNzR/u+mctZo79uZAOAZDdn4BmhLgWS+v0ZPmfmgJQctX+7DLvXtE0r7XwUNQMgKPcQFazxCm7b4jyfB4HAFaaovYWVx4+u6LBZy5gabBWa6Oa0lShx/dlcALAelaLd8o76YqmoEcJGgBQeI0HaqyaijUkq7seT2VoeGBhMXOo82YDnB6eh5wZgBbgT0koXdrShiKfH2MB5KGNFaL0GbB998UUgBRl1V4vy32ocTz8XIC525bBHrSchNNnS30m4GtbtELlecdnHQd47N07s/UsdWGYg+cBrhsIIm0q5qnq811RUwB2j9JXSlJUYhbwnOYQwHVlxXVusu1Qk1v5+HGGnQLQVByvs7wKqQa4+YBsirpb2eZ/NLXXA6ao2GH3YC0gpijOBdzmhT8Aluvg5+l+nGEHB97xjr8s/gsAAP//tq28Lpz6etIAAAAASUVORK5CYII=",
    "qr_code": "https://qr.id.vin/TX.20200302T00100015912",
    "order_id": "20200302T00100015912",
    "expiration": 1583123381
  }
}
BODY;

    /**
     * @return Response
     */
    protected function _createResponse()
    {
        return new Response(self::$strBody, self::$strHeaders);
    }

    public function testGetHeader()
    {
        $res = $this->_createResponse();
        $this->assertEquals('close', $res->getHeader('Connection'));
    }

    public function testGetHeaders()
    {
        $res = $this->_createResponse();
        $this->assertEquals(self::$expectedHeaders, $res->getHeaders());
    }

    public function testGetBodyMetaData()
    {
        $body = json_decode(self::$strBody, true);
        $res = $this->_createResponse();
        $this->assertEquals($body, $res->getBody());
        $this->assertEquals($body['meta'], $res->getMeta());
        $this->assertEquals($body['data'], $res->getData());
    }

    public function testGetHttpStatus()
    {
        $res = $this->_createResponse();
        $this->assertEquals(200, $res->getHttpStatusCode());
        $this->assertEquals("200 OKAhihi", $res->getHttpStatusText());
    }

    public function testGetApiMetaStatus()
    {
        $res = $this->_createResponse();
        $this->assertEquals(2001, $res->getApiStatusCode());
        $this->assertEquals("OK Done", $res->getApiStatusMessage());
    }

}
