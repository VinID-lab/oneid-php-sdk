<?php

require 'vendor/autoload.php';

use OneId\Order;

$apiKey = '28f04867-c6d5-425f-9108-0d90a0c0b09e';
$privatekey = <<<PKEY
-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEA6M9OQrNz6tC36ehKYiH+5/AUi7Vorf772OzFYebztnGEm0vi
PsD2Rs8nB1SibzdSOXN9NSLb81cmN01MaQO7m6jD4yyCjXcCgHcGBm5SpC2/3ZTv
C5dBF54BYcA/4bCsFkiyyMcaRUV64SE9LyGZR5YljZIHrs8AzPnMqmzb4z8i8VD8
5JirBC/0elEGNMiVkBCwwbOE8frOFGrgR10q4r7XXvV7SgeZGBKyZs9yJGc/+m7s
gkR/kSwukV6S+fyE/aYyJOdNtmaOjbG0tiWxpfpdOo5HZS7t2i4UgmvS44wRhs4s
WVRVWllHD1gavdqjCDY327yMebQelVQRuh0p3QIDAQABAoIBADiWu1UnPZPkK/Al
UH9N+CH0j2nuLIWupxTxaIEFnFPKgAsnhNTwHmzTyY7Uma8i6U+hrNuPn5skodtr
ZJlaGO4bNZIwrYMpXGhRhhtmEZxqqmp32yscFuxgscFK87wuL4YzIZIAI3iXDOlQ
JQkhx5pI+tPQnSF2m6E3TvJWB5TSu0FlwmIISjWQh/bxT11gPfxrr5ffoYQP7BnN
S+QYD1dF5JxRF3uHbUlP34tsA2NrDuFx0UGzoACrphy6uujXa/QP8MZxeKty2MS1
H3Re9Yiux/wtS/SQBBKpsiasDKuEci/Cpy5vJPzjUzO42BQN7QZciji8LBCDCYnQ
m5mPWAECgYEA9pdIUzvA5tPmPjXe+pa8K7DFfIf52FPci1jeZjVwLqQU3WmJEXkm
xopPNAf4YwuDgaauiaNlOOzRPJNDwODPMdpDd9WJaUP8zkrgEXdQxr+IaBFoRFR6
eKBiY753M+8q3H8rhZbhf/5fEog2vkbg+4X/uT11PmJ16ICueok6cQECgYEA8bFo
acRw2SWKg9jh7AMa2t40zUEInxdkzoQ+kqrZ/9K3SNQQ+f0fCJo3L6MlX2TENW6k
WSv+18CMVTRgcW0ffdRKD/2hFJxlgUXHD5FEB3qbus9kK3fgcr1yhPOuX0JFSB43
er3Y46ETHNsEPrhhnKP3rrZYdI1XkcOeNSjNnN0CgYEAlvXMcDAfivBkfudJ540K
C5E/hVpVKQtF9ATmuhmy4MrQfy/RmuHZTCh2Dntmo3P0ARZCub5PBIduHLBnBRhb
n8BoF1+hrNDTXpNWEztBNzsgTd2CQHqbM2e0dC2xGhkr2yr7QOA6krnuCBFduiT4
LOM+x2+JbDSozDqjFh8WqQECgYEAiBU/mO3GLD169CmVavGEmV8rk7XeSNU/KRhE
swgHnobiM9tTg64FXy6Vi/jr/f4ai4s0dhDTeF8tpHvNIZAzfwGcgcxoedZlQJgt
MJK2Hw/lxxUmbWMduPz6EemycGg74hNBYZarG9+Bh2m5xibCrxOTTYfV3ioG+EeQ
OJY0zvECgYEA0+44q/vHNsko0O1FvNSeB+4JPZDCHr7rlwRrw1+ijDyX7qvuGo0f
AhNdmuNwWZg8emFFEKD/3zeymSvLN47q6BnKVPbQv4TukCspMgUi6RNeZQRQ0lkz
NDYYB1O4E5bCJHazBSz5jsc/UyxbTwax1vVGr2aXIgsbLVAoQoZzznM=
-----END RSA PRIVATE KEY-----
PKEY;

putenv("ONEID_API_KEY=".$apiKey);
putenv("ONEID_PRIVATE_KEY=".$privatekey);

$callbackURL = "https://localhost";
$description = "This is a sample order";
$amount = 10000; // In VND
$storeCode = "SBLONGPV2";
$posCode = "SBLONGPV2";
$orderReferenceId = "order's ID in your system.";

$order = new Order(
    $callbackURL,
    $description,
    $amount,
    $storeCode,
    $posCode,
    $orderReferenceId
);

$qr = $order->getQRData();

// print to HTML Img tag
// printf('<img src="%s" />', $qr->getImgSrcAttr());

// print to console
print_r($qr->getImgData());