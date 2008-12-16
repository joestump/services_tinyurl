--TEST--
PEAR Bug #12207: Add User-Agent for URL lookup
--FILE--
<?php

require_once 'tests-config.php';
require_once 'Services/TinyURL.php';

try {
    $tiny = new Services_TinyURL();
    $src = $tiny->lookup('http://tinyurl.com/38aqbc');
    echo $src . "\n";
} catch (Services_TinyURL_Exception $e) {
    echo $e->getMessage() . "\n";
}

?>
--EXPECT--
http://digg.com/apple/I_just_got_my_iPhone_back_from_Apple_and_LOL
