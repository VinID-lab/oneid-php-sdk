<?php

namespace OneId\Models;

use OneId\Api\Response;

/**
 * Class QRData Represend data for an QR image
 * @package OneId\Models
 */
class LoyaltyQuantityData
{
    /**
     * Create this QRData from API's response
     * @param Response $response
     *
     * @return LoyaltyQuantityData
     */
    static public function createFromResponse($response)
    {
        return $response->getData();
    }
}