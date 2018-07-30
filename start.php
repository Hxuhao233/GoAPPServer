<?php
use Workerman\Worker;
require_once './tcp_server.php';
//require_once './http_server.php';

Worker::runAll();

?>