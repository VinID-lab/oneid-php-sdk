<?php


namespace OneId\Models;


use OneId\Api\Response;

/**
 * Class QRData Represend data for an QR image
 * @package OneId\Models
 */
class StatusData
{
    protected $created_at;
    protected $merchant_user_id;
    protected $order_amount;
    protected $order_id;
    protected $pay_status;
    protected $point_amount;
    protected $transaction_id;
    protected $updated_at;
    protected $vnd_amount;

    /**
     * Create this QRData from API's response
     * @param Response $response
     *
     * @return StatusData
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $status = new StatusData();

        $status->created_at = $data['created_at'];
        $status->merchant_user_id = $data['merchant_user_id'];
        $status->order_amount = $data['order_amount'];
        $status->order_id = $data['order_id'];
        $status->pay_status = $data['pay_status'];
        $status->point_amount = $data['point_amount'];
        $status->transaction_id = $data['transaction_id'];
        $status->updated_at = $data['updated_at'];
        $status->vnd_amount = $data['vnd_amount'];

        return $status;
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
        return $this->order_id;
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