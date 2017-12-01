<?php
class User
{
    public static function getUserInfo($accessToken, $code)
    {
        $response = Http::get("/user/getuserinfo", 
            array("access_token" => $accessToken, "code" => $code));
        return json_encode($response);
    }

	// 获取部门成员
    public static function simplelist($accessToken,$deptId){
        $response = Http::get("/user/simplelist",
            array("access_token" => $accessToken,"department_id"=>$deptId));
        return $response->userlist;

    }
	
	// 获取部门成员(详情)
	public static function lists($accessToken,$deptId){
        $response = Http::get("/user/list",
            array("access_token" => $accessToken,"department_id"=>$deptId));
        return $response->userlist;

    }
	
	// 获取订单推送信息
	public static function push_orders_lists($params){
        $response = Http::get_order($params);
        return $response->data;

    }
}