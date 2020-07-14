<?php

namespace OneId;

use Exception;
use OneId\Api\Client;
use OneId\Models\A2AOrderData;
use OneId\Models\QRData;
use OneId\Models\RefundData;
use OneId\Models\RefundDataV2;
use OneId\Models\StatusData;

/**
 * This class provide all order's feature with OneId
 *
 * Class Order
 * @package OneId
 */
class Order
{
    public $callbackURL;
    public $description;
    public $extraData;
    public $amount;
    public $point_amount;
    public $currency;
    public $orderReferenceId;
    public $posCode;
    public $serviceType;
    public $storeCode;

    public $orderId;
    public $userId;
    public $username;
    public $merchantTransactionId;
    public $transactionId;
    public $status;
    public $redirectUrl;
    public $originalOrderReferenceId;

    /**
     * @var Client
     */
    private $_client;

    /**
     * Order constructor.
     *
     * @param $callbackURL
     * @param $description
     * @param $amount
     * @param $storeCode
     * @param $posCode
     * @param null $orderReferenceId
     * @param null $extraData
     * @param string $currency
     * @param string $userId
     * @param string $username
     * @param string $redirectUrl
     * @param string $originalOrderReferenceId
     * @throws Exception
     */
    public function __construct(
        $callbackURL,
        $description,
        $amount,
        $storeCode,
        $posCode,
        $orderReferenceId = null,
        $extraData = null,
        $currency = "VND",
        $userId = "",
        $username = "",
        $redirectUrl = "",
        $originalOrderReferenceId = "",
        $point_amount
    ) {
        if (isset($this->orderID)) {
            throw new Exception("[VinID] This order already processed!");
        }

        $this->callbackURL = $callbackURL;
        $this->description = $description;
        $this->extraData = $extraData;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->orderReferenceId = $orderReferenceId;
        $this->posCode = $posCode;
        $this->serviceType = "PURCHASE";
        $this->storeCode = $storeCode;
        $this->userId = $userId;
        $this->username = $username;
        $this->redirectUrl = $redirectUrl;
        $this->originalOrderReferenceId = $originalOrderReferenceId;
        $this->point_amount = $point_amount;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if (is_null($this->_client)) $this->_client = Client::defaultClient();
        return $this->_client;
    }

    /**
     * Bind this order into a client. If you do not bind any client, Client::defaultClient()
     * will be used
     *
     * @param Client $client
     */
    public function bindClient($client)
    {
        $this->_client = $client;
    }

    /**
     * Bind callback into an Order.
     *
     * @param $status
     * @param $transID
     * @param $orderID
     */
    public function bindCallback($status, $transID, $orderID)
    {
        $this->status = $status;
        $this->transactionId = $transID;
        $this->orderId = $orderID;
    }

    /**
     * Build API body
     * @param $isWebPayment
     * @return string
     * @throws Exception
     */
    protected function buildApiRequestBody_GenTransactionQr($isWebPayment = false)
    {
        $params = [
            'callback_url' => $this->callbackURL,
            'description' => $this->description,
            'extra_data' => $this->extraData,
            'order_amount' => $this->amount,
            'order_currency' => $this->currency,
            'order_reference_id' => $this->orderReferenceId,
            'pos_code' => $this->posCode,
            'service_type' => $this->serviceType,
            'store_code' => $this->storeCode
        ];
        if ($isWebPayment) {
            if (len($this->redirectUrl) == 0) {
                throw new Exception("[VinID] You must specific a redirect URL!");
            }
            $params['redirect_url'] = $this->redirectUrl;
        }
        return json_encode($params);
    }

    /**
     * Build API body for refund
     * @return string
     * @throws Exception
     */
    protected function buildApiRequestBody_Refund()
    {
        $params = [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'merchant_transaction_id' => $this->merchantTransactionId,
            'transaction_id' => $this->transactionId,
            'user_id' => $this->userId,
            'user_name' => $this->username
        ];

        return json_encode($params);
    }

    /**
     * Build API body
     * @return string
     */
    protected function buildApiRequestBody_RefundV2()
    {
        $params = [
            "order_reference_id" => $this->orderReferenceId,
            "original_order_reference_id" => $this->originalOrderReferenceId,
            "vnd_amount" => $this->amount,
            "point_amount" => $this->point_amount,
            "description" => $this->description,
            "merchant_user_id" => $this->userId,
            "merchant_user_name" => $this->username
        ];

        return json_encode($params);
    }
    /**
     * Build API body
     * @return string
     */
    protected function buildApiRequestBody_CreateOrder()
    {
        $params = [
            'extra_data' => $this->extraData,
            'order_reference_id' => $this->orderReferenceId,
            'order_currency' => $this->currency,
            'store_code' => $this->storeCode,
            'description' => $this->description,
            'callback_url' => $this->callbackURL,
            'pos_code' => $this->posCode,
            'order_amount' => $this->amount,
            'service_type' => $this->serviceType,
            'user_id' => $this->userId
        ];
        return json_encode($params);
    }

    /**
     * Generate a QR image.
     * After you get the image data, please add it to HTML like that:
     * <img href="{qrData}" />
     *
     * @return QRData
     * @throws InvalidPrivateKeyException
     */
    public function getQRData()
    {
        $body = $this->buildApiRequestBody_GenTransactionQr();
        $client = $this->getClient();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_TRANSACTION_QR, $body);
        return QRData::createFromResponse($rv);
    }

    /**
     * Get web payment URL.
     * @return QRData
     * @throws InvalidPrivateKeyException
     */
    public function getWebPaymentUrl()
    {
        $body = $this->buildApiRequestBody_GenTransactionQr();
        $client = $this->getClient();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_TRANSACTION_QR, $body);
        return QRData::createFromResponse($rv);
    }

    /**
     * Verify OneID callback
     * After get callback from OneID, this function help you verify it with public key provided from OneID.
     * @param $signature
     * @return bool true if signature valid, false if signature invalid
     * @throws InvalidParamsException
     */
    public function verifyCallbackSignature($signature)
    {
        if ($signature == '') {
            throw new InvalidParamsException("[OneID] Signature cannot be empty!");
        }
        $oneIDPubKey = Utilities::readValueFromEnv("TEST_SANDBOX_ONEID_PUBLIC_KEY");
        if ($oneIDPubKey == '') {
            throw new InvalidParamsException("[OneID] Public key cannot be empty!");
        }
        $data = $this->status . ";" . $this->transID . ";" . $this->orderID;
        $ok = openssl_verify($data, base64_decode($signature), $oneIDPubKey, "sha256WithRSAEncryption");
        if ($ok == 1) {
            return true;
        } else if ($ok == 0) {
            return false;
        } else {
            throw new Exception("[OneID] Verify failed with OpenSSL!");
        }
    }

    /**
     * Refund an order.
     *
     * @return RefundData
     * @throws InvalidPrivateKeyException
     */
    public function refund()
    {
        $body = $this->buildApiRequestBody_Refund();
        $client = $this->getClient();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_REFUND, $body);
        return RefundData::createFromResponse($rv);
    }

    /**
     * Refund an order version 02.
     *
     * @return RefundDataV2
     * @throws InvalidPrivateKeyException
     */
    public function refund_v2()
    {
        $body = $this->buildApiRequestBody_RefundV2();
        $client = $this->getClient();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_REFUND_V2, $body);
        return RefundDataV2::createFromResponse($rv);
    }

    /**
     * Check current order status.
     *
     * @return StatusData
     * @throws InvalidPrivateKeyException
     * @throws InvalidParamsException
     */
    public function queryOrderStatus()
    {
        if (empty($this->orderId)) {
            throw new InvalidParamsException("[OneID] Order's instance is not contain valid ID!");
        }
        $client = $this->getClient();
        $rv = $this->getClient()->request("GET", Url::API_ENDPOINT_QUERY_ORDER_STATUS . $this->orderId, "");
        return StatusData::createFromResponse($rv);
    }

    /**
     * Create new App to App order.
     *
     * @return A2AOrderData
     * @throws InvalidParamsException
     * @throws InvalidPrivateKeyException
     */
    public function createA2AOrder()
    {
        if (isset($this->orderId)) {
            throw new InvalidParamsException("[OneID] Order's instance already defined!");
        }
        $body = $this->buildApiRequestBody_CreateOrder();
        $client = $this->getClient();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_CREATE_ORDER, $body);
        return A2AOrderData::createFromResponse($rv);
    }
}