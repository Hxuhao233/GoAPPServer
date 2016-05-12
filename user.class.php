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
		//var_dump($userData);
		$returnData;
		$userAccount = $userData["account"];
		$userPassword = $userData["password"];
		//$conditions = "`name` = \"$userName \"AND `password` = MD5(\"$userPassword\" )";

		$result = $mysqli->select("COUNT(*)",$userData);
		if($mysqli->getLink()->affected_rows==1){
			$updateData = array(
					'status' => '1'
					);
			$conditions = $userData;
			if($mysqli->update($updateData,$conditions)){
				if($mysqli->getLink()->affected_rows==1){
					$col = "name";
					var_dump($conditions);
					$result = $mysqli->select($col,$conditions);
					$name = $result->fetch_assoc()["name"];
					$returnData=array(
								"action"=>"Login",
								"code" => 200,
								"data" => array("name" => $name)
								);	//成功返回用户名
					return $returnData;
				}else{
					$returnData=array(
								"action"=>"Login",
								"code" => 202
								);	//已登录
					return $returnData;
				}
			}
		}else{
			$returnData=array(
						"action"=>"Login",
						"code" => 204
						);			//账号或密码错误
			return $returnData;
		}
		$returnData = array(
					"action"=>"Login",
					"code" => 201
					);
		return $returnData;


	}

	/**
	 * 用户登出
	 *  @param  array  $userName 用户名
	 * @return [bool]           [成功 TRUE 失败 FALSE]
	 */
	public static function logout($userData){
		$mysqli = new mysqlHandler("GoAPP","User");
		$returnData;

		//$conditions = "`name` = \"$userName \"AND `password` = MD5(\"$userPassword\" )";
		$updateData = array(
					'status' => '0'
					);
		$conditions =$userData;

		$result = $mysqli->update($updateData,$conditions);
		//var_dump($result);
		if($mysqli->getLink()->affected_rows==1){
			$returnData = array(
						"action" => 200,
						"action" => "Logout "
						);
			return $returnData;
		}
		$returnData = array(
						"action" => 201,
						"action" => "Logout "
						);
		return $returnData;
		

	}

	/**
	 * 用户注册
	 * @param  array  $userData [0] 用户名 [1] 密码
	 * @return [bool]           [成功 TRUE 失败 FALSE]
	 */
	public static function signIn($userData = array()){
		$mysqli = new mysqlHandler("GoAPP","User");
		$userAccount = $userData["account"];
		$col = "COUNT(*)";
		//$conditions =  "`name` = \"".$userName ."\"";
		$conditions = array(
					'account' => $userAccount
					);

		if($result = $mysqli->select($col,$conditions)){
			$colnum=$result->fetch_assoc()["COUNT(*)"];
			echo $colnum;
			if($colnum==0){
				$userName = $userData["name"];
				$userPassword = $userData["password"];
				$insertData = array(
							'account' => $userAccount,
							'name' => $userName,
							'password' => $userPassword,
							);

				if($result = $mysqli->insert($insertData)){
					$returnData = array(
								"action"=>"Signin",
								"code"=>200
								);
					return $returnData;
				}

			}else{
				$returnData = array(
							"action"=>"Signin",
							"code"=>202
							);
				return $returnData;
			}
		}
		$returnData = array(
					"action"=>"Signin",
					"code"=>201
					);
		return $returnData;
	}

	/**
	 * 设置离线消息
	 * @return [type] [description]
	 */
	public static function setOfflineMsg($data=array()){
		$mysqli = new mysqlHandler("GoAPP","offlineMsg");
		$result = $mysqli->insert($data);
	}


	/**
	 * 获取离线消息
	 * @return [type] [description]
	 */
	public static function getOfflineMsg($name){
		$mysqli = new mysqlHandler("GoAPP","offlineMsg");
		$col = "*";
		$conditions = array(
					'receiver' => $name
					);
		$i=0;
		$arr=array();
		if($result = $mysqli->select($col,$conditions)){
		    	while ($row = mysqli_fetch_row($result)) {
		    		$arr[$i++]=array(
			   				'sender'=>$row[1],
			    				'receiver'=>$row[2],
			    				'meg'=>$row[3]
			    				);
    		}

    	return $arr;
    }


    return null;
	}



}

//test
/*
$user1 = new user;
//$user1->setOfflineMsg(array('sender'=>"hexuhao",'receiver'=>"you",'msg'=>"12345"));
$arr=$user1->getOfflineMsg("you");
print_r($arr);

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