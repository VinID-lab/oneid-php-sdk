<?php

namespace OneId;
const _CLASS_PREFIX = __NAMESPACE__ . '\\';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'init.php';


/**
 * Autoloader for classes in OneId namespace
 * @param string $class
 * @return bool
 */
function oneid_autoloader($class)
{
    if (substr($class, 0, strlen(_CLASS_PREFIX)) != _CLASS_PREFIX) {
        return false;
    }
    $class = substr($class, strlen(_CLASS_PREFIX));
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    include_once __DIR__ . DIRECTORY_SEPARATOR . $class . ".php";
    return true;
}

spl_autoload_register('OneId\oneid_autoloader');