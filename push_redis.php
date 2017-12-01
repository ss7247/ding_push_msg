<?php
/*
* 新的需求，需要将信息发给被送者
* 为了尽可能 少的更改原有系统，少进行数据库连接
* 用到redis 的订阅和发送相关功能
* （此文件是发送信息给订阅的文件）
* ·linux测试 php push_redis.php ·
* 在原有系统将要推送的内容发送给相关订阅者
*
*
* 由于redis 推送时不能传数组，需要将序列化或json，我选择了序列化
*/
$redis = new Redis();
$redis->connect('192.168.10.2', 6379);
$message=[
	'times'=>time(),
	'user_id'=>'11',
	'user_name'=>'asr测试w',
	'total'=>'10.25',
	'sales_id'=>1,
];
$ret=$redis->publish('ding_order_info',serialize($message));
$redis->close();

