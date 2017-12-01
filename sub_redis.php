<?php
/*
* 新的需求，需要将信息发给被送者
* 为了尽可能 少的更改原有系统，少进行数据库连接
* 用到redis 的订阅和发送相关功能
* （此文件是订阅相关文件，需要在后台跑）
* ·linux后台运行 nouhp php ~/sub_redis.php & ·
*  如果要杀死这个进程 
*  1) 获得文件的进程号 ： ps -ef | grep sub_redis.php 
*  2）杀死进程 ： kill -9 进程号
*/
require_once(__DIR__ . "/sendpush_order_info.php");

ini_set('default_socket_timeout', -1);  //不超时
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$result=$redis->subscribe(array('ding_order_info'), 'callback');

function callback($redis,$channel,$msg){
	if($msg){
		// 接受到推送处理方法
		sendpushOrderInfo::sendInfo($msg);
	}
}
