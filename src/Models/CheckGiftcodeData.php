<?php


namespace OneId\Models;


use OneId\Api\Response;

/**
 * Class CheckGiftcodeData
 * @package OneId\Models
 */
class CheckGiftcodeData
{
    /**
     * Create this CheckGiftcodeData from API's response
     * @param Response $response
     *
     * @return CheckGiftcodeData
     */
    static public function createFromResponse($response)
    {
        $data = $response->getData();
        $loyalty = new CheckGiftcodeData();
        $loyalty->status = $data['status'];
        $loyalty->expire_date = $data['expire_date'];
        return $loyalty;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getExpireDate()
    {
        return $this->expire_date;
    }
}