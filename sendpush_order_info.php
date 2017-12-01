<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/util/Log.php");
require_once(__DIR__ . "/util/Cache.php");
require_once(__DIR__ . "/api/Auth.php");
require_once(__DIR__ . "/api/User.php");
require_once(__DIR__ . "/api/Message.php");
require_once(__DIR__ . "/api/Department.php");

/*
* 发送相关文件
*/
class sendpushOrderInfo 
{
	/*
	* 业务员ID对应钉钉ID
	* 系统原因，业务员ID无法直接找对应的钉ID，所以提前请求好相关接口获得对应钉钉ID
	* (同时也防止不断的请求钉钉接口，降低网络吞吐量)
	*/
	public static function  get_sales_id_or_ding_id(){
			return array (
				'**'=> 'dingId', 	// 接收人的ID
				'**'=> 'dingId',	// 接收人的ID
				
		);
	}
	// 获得Token
	public static function getAccessTokens()
    {
        $accessToken = Auth::getAccessToken();
		return $accessToken;
    }
	
	// 发送信息
	public static function sendInfo($arrs)
    {
		 $arr = unserialize($arrs);
		 if(!$arr)
			 return true;
		 
		$accessToken = self::getAccessTokens();
		
		$sales_id_arr =  self::get_sales_id_or_ding_id() ;		
		$userid_z = isset($sales_id_arr [$arr['sales_id']])?$sales_id_arr [$arr['sales_id']]:'';
		if(!$userid_z)
			return true;
		 
		//  发送总信息
		$content =  '下单时间:'.date('Y-m-d H:i:s',$arr['times'])
					."\n".'餐厅编码:'.$arr['user_id']
					."\n".'餐厅名称:'.$arr['user_name']
					."\n".'下单金额:'.$arr['total'];				
		$msg_push=self::send_user_push($accessToken,$userid_z,$content);
		//print_r($msg_push);
    }
	
	// 钉钉推送信息
	public static function send_user_push($accessToken,$touser,$content){
		$option = array(
				"touser"=>$touser,
				"agentid"=>AGENTID,
				"msgtype"=>"text",
				"text"=>array("content"=>$content)
			);
		$response = Message::send($accessToken,$option);
		return $response;
	}
}


