<?php

namespace OneId\Models;

use OneId\Api\Response;

/**
 * Class RefundDataV2 represent data for a RefundV2 Order
 * @package OneId\Models
 */
class RefundDataV2
{
    /**
     * @var string
     */
    protected $original_loyalty_transaction_id;

    /**
     * @var string
     */
    protected $refund_transaction_id;

    /**
     * @var string
     */
    protected $refund_loyalty_transaction_id;

    /**
     * @var string
     */
    protected $refund_transaction_wallet_id;

    /**
     * @return string
     */
    public function getOriginalLoyaltyTransactionId()
    {
        return $this->original_loyalty_transaction_id;
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
    public function getRefundLoyaltyTransactionId()
    {
        return $this->refund_loyalty_transaction_id;
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
     * @return RefundDataV2
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $order = new RefundDataV2();
        $order->original_loyalty_transaction_id = $data['original_loyalty_transaction_id'];
        $order->refund_transaction_id = $data['refund_transaction_id'];
        $order->refund_loyalty_transaction_id = $data['refund_loyalty_transaction_id'];
        $order->refund_transaction_wallet_id = $data['refund_transaction_wallet_id'];
        return $order;
    }
}
