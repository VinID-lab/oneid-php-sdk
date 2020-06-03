[![coverage report](https://gitlab.id.vin/merchant-universal/is-lib/lib-php/badges/master/coverage.svg)](https://gitlab.id.vin/merchant-universal/is-lib/lib-php/commits/master)
[![pipeline status](https://gitlab.id.vin/merchant-universal/is-lib/lib-php/badges/master/pipeline.svg)](https://gitlab.id.vin/merchant-universal/is-lib/lib-php/commits/master)

# WIP: _This is in-progress document_

# Installation
Thư viện này giúp đối tác kết nối dễ dàng đến hệ thống Payment của VinID.

# Contributions

Bạn cứ tạo Pull-Request tự nhiên

## Testing
```
./vendor/bin/phpunit tests -v  --testdox
```

# Cài đặt

```
composer require oneid/integration-lib
```

Hoặc clone repo và sử dụng thư mục `src`

# Sử dụng

## Khởi tạo client

Xem quy trình tích hợp tại [https://developers.vinid.net/gioi-thieu-chung/huong-dan-quy-trinh-tich-hop-chung] 

Có 2 cách để khởi tạo client:

1. Qua biến môi trường, client mặc định sẽ tự nhận diện.
2. Set trực tiếp vào client

### Qua biến môi trường

1. `ONEID_PRIVATE_KEY`: Chứa giá trị của private key
2. `ONEID_API_KEY`: Chứa giá trị của API-Key

### Set trực tiếp vào client

```php
use OneId\Api\Client;

Client::defaultClient()->setPrivateKey("...");
Client::defaultClient()->setApiKey("...");
```

## Generate Transaction QR

```php
use OneId\Order;

$callbackURL = "https://localhost";
$description = "This is a sample order";
$amount = 10000; // In VND
$storeCode = "You get this from merchant site";
$posCode = "You get this from merchant site";
$orderReferenceId = "order's ID in your system.";

$order = new Order(
    $callbackURL,
    $description,
    $amount,
    $storeCode,
    $posCode,
    $orderReferenceId,
);

$qr = $order->getQRData();

printf('<img src="%s" />', $qr->getImgSrcAttr());
```