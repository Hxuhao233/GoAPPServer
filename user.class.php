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
		$mysqli = new mysqlHandler("GoAPP","user");
		$col="id";
		$returnData;
		$result;
		$conditions;
		$userID;
		
		$result = $mysqli->select($col,$userData);
		// 验证账号密码
		if($mysqli->getLink()->affected_rows==1){
			$userID = $result->fetch_assoc()["id"];
			echo "userID :" .$userID;
			$updateData = array(
					'status' => '1'
					);
			$conditions = $userData;
	
			if($mysqli->update($updateData,$conditions)){
				//if($mysqli->getLink()->affected_rows==1){
					// 查询昵称
					$col = "Name";
					
					$conditions=array(
						'UserId' =>$userID
						);
					$mysqli->changeTable("information");
					$result = $mysqli->select($col,$conditions);
					$name = $result->fetch_assoc()["Name"];
					$returnData=array(
								"action"=>"Login",
								"code" => 200,
								"data" => array(json_encode(array("name" => $name)))
								);	//成功返回用户名
					var_dump($returnData);
					return $returnData;
				/*}else{
					$returnData=array(
								"action"=>"Login",
								"code" => 205
								);	//已登录
					return $returnData;
				}*/
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
		$mysqli = new mysqlHandler("GoAPP","user");
		$returnData;

		//$conditions = "`name` = \"$userName \"AND `password` = MD5(\"$userPassword\" )";
		$updateData = array(
					'status' => '0'
					);
		$conditions =$userData;

		var_dump($conditions);
		$result = $mysqli->update($updateData,$conditions);
		
		if($mysqli->getLink()->affected_rows==1){
			$returnData = array(
						"code" => 200,
						"action" => "Logout "
						);
			return $returnData;
		}
		$returnData = array(
						"code" => 201,
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
		$mysqli = new mysqlHandler("GoAPP","user");
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
				// 往user表添加
				$userName = $userData["name"];
				$userPassword = $userData["password"];
				$insertData = array(
							'account' => $userAccount,
							'password' => $userPassword,
							);

				if($result = $mysqli->insert($insertData)){
					// 往infomation表里添加
					$id = $mysqli->getLink()->query("select LAST_INSERT_ID()")->fetch_assoc()["LAST_INSERT_ID()"];

					$insertData = array(
							'UserId' => $id,
							'Name' => $userName,
							'Question' => $userData["question"],
							'Answer' => $userData["answer"]
							);
					$mysqli->changeTable("information");
					$mysqli->insert($insertData);
					if($result = $mysqli->insert($insertData)){
						$returnData = array(
								"action"=>"Signin",
								"code"=>200
								);
						$result->close();
						return $returnData;
					}
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
	 * 获取信息
	 * @param  account  用户账户
	 * @return array ['ID']['Sex']['Age']['School']['Phone']['Account']['Name']['Status']
	 */
	public static function getInformation($account){
		$mysqli = new mysqlHandler("GoAPP","information");
		$col = "*";
		$arr;
		$returnData;
		$conditions = array(
					'Account' => $account
					);
		if($result = $mysqli->select($col,$conditions)){

			$row=mysqli_fetch_row($result);
			if($row[7]==2){//status,0=>保密,1=>对好友公开，2=>公开
				$arr = array(
					'ID'=>$row[0],
					'Sex'=>$row[1],
					'Age'=>$row[2],
					'School'=>$row[3],
					'Phone'=>$row[4],
					'Account'=>$row[5],
					'Name'=>$row[6],
					'Status'=>$row[7],
					);
        			//return $arr;
        			$returnData = array(
        					"action"=>"SearchPerson",
        					"code"=>200,
        					"data"=>$arr
        					);
			}
		}

		//return null;
		$returnData = array(
				"action"=>"SearchPerson",
				"code"=>207
				);

		return $returnData;
	}

	/**
	 * 设置用户头像
	 * @param [type] $data [account] 账号 [icon]头像 [type]格式
	 */
	public static function setIcon($data){
		$returnData=array(
					"action"=>"ModifyInfo"
				);

		$Icon = $data["icon"];
		$account = $data["account"];
		$IconName = "userIcon/".$account . $data["type"];
		
		if(file_put_contents($IconName, data)!=false){
			$returnData["code"]=200;
		}else{
			$returnData["code"]=301;
		}

		return $returnData;
	}

	/**
	 * 获取用户头像
	 * @param  [array] $data [account] 账号 
	 * @return [array]  $returnData     	[code] 200 [data] array 
	 *                                  			[code] 302
	 */
	public static function getIcon($data){
		$returnData;

		$account = $data["account"];
		$IconName = "userIcon/".$account ."*";
		$icon = file_get_contents(filename);
		if($icon!=false){
			$returnData["code"]=200;
			$returnData["data"]=array(
							"account"=>$account,
							"icon"=>$icon
						);
		}else{
			$returnData["code"]=302;
		}

		return $returnData;
	}




	/**
	 * 添加好友
	 * @param  array   [USER01]  [USER02]
	 * @return []
	 */
	public static function makeFriends($data=array()){
		$returnData= array(
    			"action"=>"AddFriend"
    			);
		$col = "COUNT(*)";
		$result;
		$mysqli = new mysqlHandler("GoAPP","Friends");
		usort($data, strnatcmp);
		$result=$mysqli->select($col,$data);
    		if($result->fetch_assoc()[$col]==0){
    			if($result = $mysqli->insert($data)){
    				$returnData["code"]=200;
    				
    			}
    		}else{
    			$returnData["code"]=208;
    			
    		}
    		$result->close();
    		return $returnData;

   		

	}


	/**
	 * 忘记密码1
	 */
	public function forgetPWD1($data=array()){

		$mysqli = new mysqlHandler("GoAPP","user");
		$account = $mysqli->clear($data["account"]);
		$sql = "select `Question` ,`Answer` from `information`where `UserID` = 
				(select `id` from `user` where  `account` = \"$account\")";
		$res;
		$returnData = array(
			"action" => "forgetPWD1"
			);
		// 查询密码保护问题和答案
		if($res = $mysqli->excute($sql)){
			$QA = $res->fetch_assoc();
			//var_dump($QA);
			$returnData["code"] = 200;
			$returnData["data"] = array(
				"Question" => $QA["Question"],
				"Answer" => $QA["Answer"]
				);
			$res->close();
		}else{
			$returnData["code"] = 207;
		}
		
		return $returnData;

	}


	/**
	 * 忘记密码2
	 */
	public function forgetPWD2($data=array()){
		$mysqli = new mysqlHandler("GoAPP","user");
		$account = $mysqli->clear($data["account"]);
		$password = $mysqli->clear($data["password"]);
		$updateData = array(
			"password" => $password
			);
		$conditions = array(
			"account" => $account
			);
		$res;
		$returnData = array(
			"aciton" => "forgetPWD2"
		);
		$res = $mysqli->update($updateData,$conditions);
		if($mysqli->getLink()->affected_rows == 1){
			$returnData["code"] = 200;
		}else{
			$returnData["code"] = 207;
		}

		return $returnData;
	}






	/**
	 * 删除好友
	 * @param   array  [USER01]   [USER02]
	 * @return []
	 */
	public static function deleteFriends($data=array()){
   		$mysqli = new mysqlHandler("GoAPP","Friends");
    		$col = "*";
		$conditions = array(
					'USER01' => $data["USER01"],
					'USER02' => $data["USER02"]
					);
		if(count($result = $mysqli->select($col,$conditions))<0){
			$row=mysqli_fetch_row($result);
			$mysqli->delete($row[0]);
		}
		else{
			$conditions = array(
					'USER01' => $data["USER02"],
					'USER02' => $data["USER01"]
					);
			$result = $mysqli->select($col,$conditions);
			$row=mysqli_fetch_row($result);
			$mysqli->delete($row[0]);
			$result->close();
		}

	}

}

//test

//$user1=new user;
//	'USER01'=>"456",
//	'USER02'=>"123"
//	);
//$user1->makeFriends($data);
//$user1->deleteFriends($data);
//$arr=$user1->getInformation("123");
//print_r($arr);
/*
$user1 = new user;
//$user1->setOfflineMsg(array('sender'=>"hexuhao",'receiver'=>"you",'msg'=>"12345"));
$arr=$user1->getOfflineMsg("you");
print_r($arr);
*/
/*$res = $user1->login(array("account" => "13710685836", "password" => "12345"));
var_dump($res);
*/
/*
if($user1->logout(array("Hxuhao233","12345")))
	echo "logout succeed";
else
	echo "logout failed\n";

/*
if($user1->signIn(array("Hxuhao233","何徐昊","12345")))
	echo "sign in succeed\n";
else
	echo "sign in failed\n";
*/
/*
$info=array(
	"account" => "159753",
	"password" => "12345",
	"name" => "何徐昊",
	"question" => "我的儿子是谁",
	"answer" => "宋丹"
	);
$res =$user1->signIn($info);
var_dump($res);
*/
/*
// 忘记密码
$info = array(
	"account" => "13710685836"
	);
$returnData = $user1->forgetPWD1($info);
var_dump($returnData);
$info=array(
	"account" => "159753",
	"password" => "1234590"
	);
$returnData = $user1->forgetPWD2($info);
var_dump($returnData);*/
?>