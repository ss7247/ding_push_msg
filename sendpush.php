<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/util/Log.php");
require_once(__DIR__ . "/util/Cache.php");
require_once(__DIR__ . "/api/Auth.php");
require_once(__DIR__ . "/api/User.php");
require_once(__DIR__ . "/api/Message.php");
require_once(__DIR__ . "/api/Department.php");


@set_time_limit(0);
// 获得Token码 （下面这个方法有文件缓存，不每次都请求）
$accessToken = Auth::getAccessToken(); 
// 发送信息
$msg = push_smg($accessToken);



// 推送信息入口
function push_smg($accessToken){
	// 1. 获得获得发送信息数据
	$push_data = get_push_data();
	if(!$push_data)
		return 'error:获得获得发送信息数据';

	// 2. 获得 某部 所有成员(详情)	
	$user_lists = get_user_lists($accessToken,"部门ID");
	if(!$user_lists)
		return 'error:获得 某部 所有成员(详情)';
	// 由于钉钉返回的是对象，下面将对象转成数组
	$user_lists = object2array($user_lists);

	// 3. 比对成员与数据(销售人员今日下单餐厅数)
	$order_user_count	= 0;//下单餐厅数
	$order_count		= 0;//下单数量
	$order_money		= 0;//下单数金额
	$times 				= date('Y-m-d H:i');//截止时间
	foreach($user_lists as $p){
	
		$order_user_count += $p['order_user_count'];
		$order_count += $p['order_count'];
		$order_money += $p['order_money'];
	}
	
	// 由于是给指定的人发，所以我通过访问钉钉接口知道了对应人的ID
	$user_list = [
		'**'=> 'dingId', 	// 接收人的ID
		'**'=> 'dingId',	// 接收人的ID
	];

	// 5. 发送总信息
	$userid_z =implode('|',$user_list);
	$content =  '截止'.$times
				."\n".'总下单餐厅数量：'.$order_user_count
				."\n".'总下单数量：'.$order_count
				."\n".'总下单金额：'.$order_money
				."\n(20:30-23:30,每小时统计一次今日下单量)";				
	$msg_push=send_user_push($accessToken,$userid_z,$content);
	print_r($msg_push);

}

// 获取部门列表
function get_department_list($accessToken){
	$department_list = Department::listDept($accessToken);
	return $department_list;
}

// 获取部门成员(详情)
function get_user_lists($accessToken,$deptId){
	$user_lists = User::lists($accessToken,$deptId);
	return $user_lists;
}

// 获得发送信息数据
function get_push_data(){
	/*
	*有关公司代码删除
	* 将需要发送的数据以json形式返回
	*/
	
}

// 发送推送信息
function send_user_push($accessToken,$touser,$content){
	$option = array(
            "touser"=>$touser,
            "agentid"=>AGENTID,
            "msgtype"=>"text",
            "text"=>array("content"=>$content)
        );
    $response = Message::send($accessToken,$option);
	return $response;
}

// 对象转数组
function object2array(&$object) {
	 $object =  json_decode( json_encode( $object),true);
	 return  $object;
}
