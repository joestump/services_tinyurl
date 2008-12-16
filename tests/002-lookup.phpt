--TEST--
Services_TinyURL::lookup()
--FILE--
<?php

require_once 'tests-config.php';
require_once 'Services/TinyURL.php';

try {
    $tiny = new Services_TinyURL();
    $src = $tiny->lookup('http://tinyurl.com/zhy5');
    echo $src . "\n";
} catch (Services_TinyURL_Exception $e) {
    echo $e->getMessage() . "\n";
}

?>
--EXPECTREGEX--
http:\/\/scripting.com\/?
