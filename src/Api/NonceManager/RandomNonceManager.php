<?php


namespace OneId\Api\NonceManager;


use OneId\Utilities;

/**
 * Class RandomNonceManager
 * @package OneId\NonceManager
 *
 * Generate nonce by GUID
 */
class RandomNonceManager implements iNonceManager
{
    public function generateNonce()
    {
        return Utilities::generateGUID4();
    }
}