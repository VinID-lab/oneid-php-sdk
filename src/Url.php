<?php
namespace OneId;

class Url {
    const API_BASEURL_SANDBOX = 'https://api-merchant-sandbox.vinid.dev';
    const API_BASEURL_PRODUCTION = 'https://api-merchant.vinid.net';
    const API_ENDPOINT_TRANSACTION_QR = '/merchant-integration/v1/qr/gen-transaction-qr';
    const API_ENDPOINT_CREATE_ORDER = '/merchant-integration/v1/qr/create-transaction-order';
    const API_ENDPOINT_QUERY_ORDER_STATUS = '/merchant-integration/v1/qr/query/';
    const API_ENDPOINT_REFUND = '/merchant-integration/v1/refund';
    const API_ENDPOINT_REFUND_V2 = '/merchant-integration/v2/refund';

    const API_ENDPOINT_CHECK_QUANTITY = '/merchant-integration/v1/loyalty/check-quantity';
    const API_ENDPOINT_GET_GIFTCODE = '/merchant-integration/v1/loyalty/get-giftcode';
    const API_ENDPOINT_GET_ORDER_DETAIL = '/merchant-integration/v1/loyalty/get-order-detail';
    const API_ENDPOINT_CHECK_GIFTCODE = '/merchant-integration/v1/loyalty/check-giftcode';
}
