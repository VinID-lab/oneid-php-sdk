<?php


namespace OneId\Models;


use OneId\Api\Response;

/**
 * Class RefundData represent data for a Refund Order
 * @package OneId\Models
 */
class RefundData
{
    /**
     * @var string
     */
    protected $original_transaction_id;

    /**
     * @var string
     */
    protected $refund_transaction_id;

    /**
     * @var string
     */
    protected $refund_transaction_wallet_id;

    /**
     * @return string
     */
    public function getOriginalTransactionId()
    {
        return $this->original_transaction_id;
    }

    /**
     * @return string
     */
    public function getRefundTransactionId()
    {
        return $this->refund_transaction_id;
    }

    /**
     * @return string
     */
    public function getRefundTransactionWalletId()
    {
        return $this->refund_transaction_wallet_id;
    }

    /**
     * Create this QRData from API's response
     * @param Response $response
     *
     * @return RefundData
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $order = new RefundData();
        $order->original_transaction_id = $data['original_transaction_id'];
        $order->refund_transaction_id = $data['refund_transaction_id'];
        $order->refund_transaction_wallet_id = $data['refund_transaction_wallet_id'];
        return $order;
    }
}