<?php


namespace OneId\Models;


use OneId\Api\Response;

/**
 * Class QRData Represend data for an QR image
 * @package OneId\Models
 */
class A2AOrderData
{


    /**
     * Create this QRData from API's response
     * @param Response $response
     *
     * @return A2AOrderData
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $order = new A2AOrderData();
        $order->imgData = $data['qr_data'];
        $order->href = $data['qr_code'];
        $order->orderId = $data['order_id'];
        $order->expiration = $data['expiration'];
        return $order;
    }

    /**
     * @return string
     */
    public function getImgData()
    {
        return $this->imgData;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Get value for img tag's src attribute
     */
    public function getImgSrcAttr()
    {
        return sprintf("data:image/png;base64,{%s}", $this->getImgData());
    }

    public function __toString()
    {
        return $this->getImgSrcAttr();
    }
}