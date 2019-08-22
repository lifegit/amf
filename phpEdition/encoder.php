<?php
require_once __DIR__ . '/infomaniac-amf/Autoloader.php';//自动载入class

header('Content-Type: application/x-amf');


print_r("open file");
$filename = "/Users/yxs/Downloads/config.json";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);


print_r("json_encode");
$json = json_encode($contents);


print_r("amf_encode");
$encode =  amf_encode($json);


print_r("write file");
$fp = fopen("/Users/yxs/Downloads/gogoog.nncc", 'w');
fwrite($fp, $encode);
fclose($fp);


print_r("ok");
