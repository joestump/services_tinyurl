--TEST--
Services_TinyURL::create() == Services_TinyURL::lookup()
--FILE--
<?php

require_once 'tests-config.php';
require_once 'Services/TinyURL.php';

try {
    $t = new Services_TinyURL();
    $url = 'http://pear.php.net';
    $tiny = $t->create($url);
    $dest = $t->lookup($tiny);
    var_dump(($url == $dest));
} catch (Services_TinyURL_Exception $e) {
    echo $e->getMessage();
}

?>
--EXPECT--
bool(true)
