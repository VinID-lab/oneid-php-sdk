<?php

namespace OneId;

use Exception;
use OneId\Api\Client;
use OneId\Models\CheckGiftcodeData;
use OneId\Models\LoyaltyOrderDetail;
use OneId\Models\LoyaltyQuantityData;

/**
 * This class provide everything related to Loyalty Giftcode
 *
 * Class Giftcode
 * @package OneId
 */
class Giftcode
{
    public $callbackURL;
    public $description;
    public $extraData;
    public $posCode;
    public $storeCode;
    public $cardType;
    public $merchantOrderId;
    public $serial;

    /**
     * @var Client
     */
    private $_client;

    /**
     * Constructor.
     *
     * @param $callbackURL
     * @param $description
     * @param $extraData
     * @param $posCode
     * @param $storeCode
     * @param $cardType
     * @param $merchantOrderId
     * @param $serial
     */
    public function __construct(
        $callbackURL,
        $description,
        $extraData,
        $posCode,
        $storeCode,
        $cardType,
        $merchantOrderId,
        $serial
    )
    {
        $this->callbackURL = $callbackURL;
        $this->description = $description;
        $this->extraData = $extraData;
        $this->posCode = $posCode;
        $this->storeCode = $storeCode;
        $this->cardType = $cardType;
        $this->merchantOrderId = $merchantOrderId;
        $this->serial = $serial;
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
     * Build API body
     * @return string
     */
    protected function buildApiRequestBody_CheckQuantity()
    {
        $params = [
            'pos_code' => $this->posCode,
            'store_code' => $this->storeCode,
            'card_type' => $this->cardType
        ];

        return json_encode($params);
    }

    /**
     * Build API body
     * @return string
     */
    protected function buildApiRequestBody_GetGiftcode()
    {
        $params = [
            'extra_data' => $this->extraData,
            'merchant_order_id' => $this->merchantOrderId,
            'store_code' => $this->storeCode,
            'pos_code' => $this->posCode,
            'order_info' => $this->orderInfo
        ];
        return json_encode($params);
    }

    /**
     * Build API body
     * @return string
     */
    protected function buildApiRequestBody_GetOrderDetail()
    {
        $params = [
            'merchant_order_id' => $this->merchantOrderId,
            'store_code' => $this->storeCode,
            'pos_code' => $this->posCode
        ];
        return json_encode($params);
    }

    /**
     * Build API body
     * @return string
     */
    protected function buildApiRequestBody_CheckGiftcode()
    {
        $params = [
            'serial' => $this->serial,
            'store_code' => $this->storeCode,
            'pos_code' => $this->posCode
        ];
        return json_encode($params);
    }

    /**
     * Get Giftcode.
     *
     * @return LoyaltyQuantityData
     * @throws InvalidPrivateKeyException
     */
    public function checkQuantity()
    {
        $body = $this->buildApiRequestBody_CheckQuantity();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_CHECK_QUANTITY, $body);
        return LoyaltyQuantityData::createFromResponse($rv);
    }

    /**
     * Get Giftcode.
     *
     * @return LoyaltyOrderDetail
     * @throws InvalidPrivateKeyException
     */
    public function refund()
    {
        $body = $this->buildApiRequestBody_GetGiftcode();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_GET_GIFTCODE, $body);
        return LoyaltyOrderDetail::createFromResponse($rv);
    }

    /**
     * Get order details.
     *
     * @return LoyaltyOrderDetail
     * @throws InvalidPrivateKeyException
     * @throws InvalidParamsException
     */
    public function getOrderDetail()
    {
        if (empty($this->merchantOrderId)) {
            throw new InvalidParamsException("[OneID] Merchant order ID cannot be empty!");
        }
        $body = $this->buildApiRequestBody_GetOrderDetail();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_GET_ORDER_DETAIL . $this->orderId, $body);
        return LoyaltyOrderDetail::createFromResponse($rv);
    }

    /**
     * Check giftcode based on serial.
     *
     * @return CheckGiftcodeData
     * @throws InvalidParamsException
     * @throws InvalidPrivateKeyException
     */
    public function checkGiftCode()
    {
        if (!isset($this->serial)) {
            throw new InvalidParamsException("[OneID] Serial cannot be empty!");
        }
        $body = $this->buildApiRequestBody_CheckGiftcode();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_CHECK_GIFTCODE, $body);
        return CheckGiftcodeData::createFromResponse($rv);
    }
}
