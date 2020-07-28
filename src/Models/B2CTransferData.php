<?php


namespace OneId\Models;


use OneId\Api\Response;

/**
 * Class QRData Represend data for an QR image
 * @package OneId\Models
 */
class B2CTransferData
{
    /**
     * Parse data from API's response
     * @param Response $response
     *
     * @return B2CTransferData
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $order = new B2CTransferData();
        $order->transactionId = $data['transaction_id'];
        $order->merchantTransactionId = $data['merchant_transaction_id'];
        return $order;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return string
     */
    public function getMerchantTransactionId()
    {
        return $this->merchantTransactionId;
    }
}