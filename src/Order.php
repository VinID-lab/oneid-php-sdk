<?php
namespace OneId;

use OneId\Api\Client;
use OneId\Models\QRData;
use OneId\Models\StatusData;
use function PHPUnit\Framework\throwException;

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
    public $currency;
    public $orderReferenceId;
    public $posCode;
    public $serviceType;
    public $storeCode;

    public $orderID;
    public $userId;

    /**
     * @var Client
     */
    private $_client;

    /**
     * Order constructor.
     * @todo -o LongPV please comment here
     * @param $callbackURL
     * @param $description
     * @param $amount
     * @param $currency
     * @param $storeCode
     * @param $posCode
     * @param null $orderReferenceId
     * @param null $extraData
     */
    public function __construct(
        $callbackURL,
        $description,
        $amount,
        $storeCode,
        $posCode,
        $orderReferenceId=null,
        $extraData=null,
        $currency="VND"
        )
    {
        $this->callbackURL = $callbackURL;
        $this->description = $description;
        $this->extraData = $extraData;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->orderReferenceId = $orderReferenceId;
        $this->posCode = $posCode;
        $this->serviceType = "PURCHASE";
        $this->storeCode = $storeCode;
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
    protected function buildApiRequestBody_GenTransactionQr()
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
            'user_id' => $this->userId,
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
     * Verify OneID callback
     * After get callback from OneID, this function help you verify it with public key provided from OneID.
     *
     * @return int 1 if the signature is correct, 0 if it is incorrect, and
     * -1 on error.
     */
    public function verifyCallbackSignature($signature, $orderStatus, $orderTransID, $orderID)
    {
        $oneIDPubKey = openssl_pkey_get_public(Utilities::readValueFromEnv("ONEID_PUBLIC_KEY"));
        if ($oneIDPubKey == '') {
            return -1;
        }
        $data = $orderStatus . ";" . $orderTransID . ";" . $orderID;
        return openssl_verify($data, $signature, $oneIDPubKey);
    }

    /**
     * Refund an order.
     *
     * @return QRData
     * @throws InvalidPrivateKeyException
     */
    public function refund()
    {
        $body = $this->buildApiRequestBody_GenTransactionQr();
        $client = $this->getClient();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_TRANSACTION_QR, $body);
        return QRData::createFromResponse($rv);
    }

    /**
     * Check current order status.
     *
     * @return StatusData
     * @throws InvalidPrivateKeyException
     */
    public function queryOrderStatus()
    {
        if (empty($this->orderID)) {
            throwException("[OneID] Order's instance is not contain valid ID!");
        }
        $client = $this->getClient();
        $rv = $this->getClient()->request("GET", Url::API_ENDPOINT_QUERY_ORDER_STATUS . $this->orderID, "");
        return StatusData::createFromResponse($rv);
    }

    /**
     * Create new order.
     *
     * @return StatusData
     * @throws InvalidPrivateKeyException
     */
    public function CreateOrder()
    {
        if (isset($this->orderID)) {
            throwException("[OneID] Order's instance already defined!");
        }
        $body = $this->buildApiRequestBody_CreateOrder();
        $client = $this->getClient();
        $rv = $this->getClient()->request("POST", Url::API_ENDPOINT_CREATE_ORDER, $body);
        return StatusData::createFromResponse($rv);
    }
}