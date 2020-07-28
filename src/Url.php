<?php
namespace OneId;

class Url {
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";

    const API_BASEURL_SANDBOX = 'https://api-merchant-sandbox.vinid.dev';
    const API_BASEURL_PRODUCTION = 'https://api-merchant.vinid.net';
    const MERCHANT_PATH = "/merchant-integration";
    const API_VERSION_01 = "/v1";
    const API_VERSION_02 = "/v2";

    const API_ENDPOINT_TRANSACTION_QR = self::MERCHANT_PATH . self::API_VERSION_01 . '/qr/gen-transaction-qr';
    const API_ENDPOINT_CREATE_ORDER = self::MERCHANT_PATH . self::API_VERSION_01 . '/qr/create-transaction-order';
    const API_ENDPOINT_QUERY_ORDER_STATUS = self::MERCHANT_PATH . self::API_VERSION_01 . '/qr/query/';
    const API_ENDPOINT_REFUND = self::MERCHANT_PATH . self::API_VERSION_01 . '/refund';
    const API_ENDPOINT_REFUND_V2 = self::MERCHANT_PATH . self::API_VERSION_02 . '/refund';

    const API_ENDPOINT_CHECK_QUANTITY = self::MERCHANT_PATH . self::API_VERSION_01 . '/loyalty/check-quantity';
    const API_ENDPOINT_GET_GIFTCODE = self::MERCHANT_PATH . self::API_VERSION_01 . '/loyalty/get-giftcode';
    const API_ENDPOINT_GET_ORDER_DETAIL = self::MERCHANT_PATH . self::API_VERSION_01 . '/loyalty/get-order-detail';
    const API_ENDPOINT_CHECK_GIFTCODE = self::MERCHANT_PATH . self::API_VERSION_01 . '/loyalty/check-giftcode';

    const API_ENDPOINT_B2C_TRANSFER = self::MERCHANT_PATH . self::API_VERSION_01 . '/money-transfer';
}
