--TEST--
Services_TinyURL::create()
--FILE--
<?php

require_once 'tests-config.php';
require_once 'Services/TinyURL.php';

try {
    $tiny = new Services_TinyURL();
    $url = $tiny->create('http://www.joestump.net');
    echo $url . "\n";
} catch (Services_TinyURL_Exception $e) {
    echo $e->getMessage();
}

?>
--EXPECTREGEX--
http:\/\/tinyurl.com\/[a-zA-Z0-9]+
