<?php

namespace OneId\Models;

use OneId\Api\Response;

/**
 * Class QRData Represend data for an QR image
 * @package OneId\Models
 */
class LoyaltyOrderDetail
{
    /**
     * Create this QRData from API's response
     * @param Response $response
     *
     * @return LoyaltyOrderDetail
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $order = new LoyaltyOrderDetail();
        $order->created_at = $data['created_at'];
        $order->extra_data = $data['extra_data'];
        $order->order_info = $data['order_info'];
        return $order;
    }
}