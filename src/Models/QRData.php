<?php


namespace OneId\Models;


use OneId\Api\Response;

/**
 * Class QRData Represend data for an QR image
 * @package OneId\Models
 */
class QRData
{
    /**
     * Base64 encoded for qr image's content
     * @var string
     */
    protected $imgData;

    /**
     * The link that QR should present
     * @var string
     */
    protected $href;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * Timestamp that the QR will expire
     * @var int
     */
    protected $expiration;

    /**
     * Create this QRData from API's response
     * @param Response $response
     *
     * @return QRData
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $qr = new QRData();
        $qr->imgData = $data['qr_data'];
        $qr->href = $data['qr_code'];
        $qr->orderId = $data['order_id'];
        $qr->expiration = $data['expiration'];
        return $qr;
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