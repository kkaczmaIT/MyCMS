<?php
$redis = new Redis();
$redis->connect("127.0.0.1", 6379);
$redis->set("test", "Witaj");
echo $redis->get("test");
?>