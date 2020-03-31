FROM tarzanjw/php-composer-xdebug

ADD . /oneid-lib
WORKDIR /oneid-lib

RUN composer install
#RUN composer require --dev phpunit/phpunit ^9
#RUN composer require --dev phpunit/php-code-coverage ^8.0

CMD ./vendor/bin/phpunit --coverage-text tests -v --testdox