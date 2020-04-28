<?php

namespace OneId;

use OneId\Api\Client;
use OneId\Models\QRData;

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
    public $transID;
    public $status;

    /**
     * @var Client
     */
    private $_client;

    /**
     * Order constructor.
     * @param $callbackURL
     * @param $description
     * @param $amount
     * @param $currency
     * @param $storeCode
     * @param $posCode
     * @param null $orderReferenceId
     * @param null $extraData
     * @todo -o LongPV please comment here
     */
    public function __construct(
        $callbackURL,
        $description,
        $amount,
        $storeCode,
        $posCode,
        $orderReferenceId = null,
        $extraData = null,
        $currency = "VND"
    )
    {
        if (isset($this->orderID)) {
            throw new \Exception("[VinID] This order already processed!");
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
        $this->transID = $transID;
        $this->orderID = $orderID;
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
        $rv = $this->getClient()->request("GET", API_ENDPOINT_TRANSACTION_QR, $body);
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
        $oneIDPubKey = openssl_pkey_get_public(Utilities::readValueFromEnv("ONEID_PUBLIC_KEY"));
        if ($oneIDPubKey == '') {
            throw new InvalidParamsException("[OneID] Public key cannot be empty!");
        }
        $data = $this->status . ";" . $this->transID . ";" . $this->orderID;
        $ok = openssl_verify($data, $signature, $oneIDPubKey);
        if ($ok == 1) {
            return true;
        } else if ($ok == 0) {
            return false;
        } else {
            throw new \Exception("[OneID] Verify failed with OpenSSL!");
        }
    }
}