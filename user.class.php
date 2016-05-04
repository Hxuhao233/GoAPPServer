<?php

require_once './mysql/mysql.class.php';
/**
 * 用户操作类
 * 实现了登录、登出、注册
 * 还有查找
 */
class user{
	/**
	 * 用户登录
	 * @param  array  $userData [0] 账号 [1]密码
	 * @return [string or bool]           [成功 $name  用户名    失败 FALSE]
	 */
	public static function login($userData=array()){
		$mysqli = new mysqlHandler("GoAPP","User");
		var_dump($userData);
		$userAccount = $userData["account"];
		$userPassword = $userData["password"];
		//$conditions = "`name` = \"$userName \"AND `password` = MD5(\"$userPassword\" )";
		$updateData = array(
								'status' => '1'
								);
		$conditions = array(
								'account' => $userAccount,
								'password' => $userPassword
								);
		if($mysqli->update($updateData,$conditions)){
			if($mysqli->getLink()->affected_rows==1){
				$col = "name";
				$result = $mysqli->select($col,$conditions);
				$name = $result->fetch_assoc()["name"];
				return $name;
			}	
		}
		
		return false;

	}

	/**
	 * 用户登出
	 *  @param  array  $userName 用户名 
	 * @return [bool]           [成功 TRUE 失败 FALSE]
	 */
	public static function logout($userName){
		$mysqli = new mysqlHandler("GoAPP","User");
		
		
		//$conditions = "`name` = \"$userName \"AND `password` = MD5(\"$userPassword\" )";
		$updateData = array(
							'status' => '0'
							);
		$conditions = array(
							'name' => $userName["name"],
						
							);		
		
		$result = $mysqli->update($updateData,$conditions);
		//var_dump($result);
		if($mysqli->getLink()->affected_rows==1){
			return true;
		}
		return false;

	}

	/**
	 * 用户注册
	 * @param  array  $userData [0] 用户名 [1] 密码
	 * @return [bool]           [成功 TRUE 失败 FALSE]
	 */
	public static function signIn($userData = array()){
		$mysqli = new mysqlHandler("GoAPP","User");
		$userAccount = $userData[0];
		$col = "COUNT(*)";
		//$conditions =  "`name` = \"".$userName ."\"";
		$conditions = array(
							'account' => $userAccount
							);
		
		if($result = $mysqli->select($col,$conditions)){
			$colnum=$result->fetch_assoc()["COUNT(*)"];
			echo $colnum;
			if($colnum==0){		
				$userName = $userData[1];
				$userPassword = $userData[2];
				$insertData = array(
							'account' => $userAccount,
							'name' => $userName,
							'password' => $userPassword,

							);

				$result = $mysqli->insert($insertData);
				return $result;
			}else{
				return false;
			}
		}
		return false;
	}

	/**
	 * 设置离线消息
	 * @return [type] [description]
	 */
	public static function setOfflineMsg(){

	}


	/**
	 * 获取离线消息
	 * @return [type] [description]
	 */
	public static function getOfflineMsg($name){

	}

	
	
}

//test
/*
$user1 = new user;

if($user1->login(array("Hxuhao233","12345")))
	echo "login succeed\n";
else
	echo "login failed\n";

if($user1->logout(array("Hxuhao233","12345")))
	echo "logout succeed";
else
	echo "logout failed\n";
*/
/*
if($user1->signIn(array("Hxuhao233","何徐昊","12345")))
	echo "sign in succeed\n";
else
	echo "sign in failed\n";
*/
?>