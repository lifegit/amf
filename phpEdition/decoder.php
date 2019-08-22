<?php
require_once __DIR__ . '/infomaniac-amf/Autoloader.php';//自动载入class


print_r("open file");
$filename = "/Users/yxs/Downloads/config.nncc";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);


print_r("amf_decode");
$json = amf_decode($contents);


print_r("write file");
file_put_contents('/Users/yxs/Downloads/filename.json', print_r($json, true));


print_r("ok");
