<?php

use Workerman\Worker;
use Workerman\Lib\Timer;
require_once './Workerman/Autoloader.php';
require_once './user.class.php';
require_once './getPOI.php';
require_once './msgHander.class.php';

/**
 * 要不要写成一个类呢= =
 */


// 创建一个Worker监听2347端口，不使用任何应用层协议
$tcp_worker = new Worker("tcp://0.0.0.0:2347");

//创建管理用户链接的数组
$tcp_worker->connectionsID = array();

// 启动1个进程对外提供服务
$tcp_worker->count = 1;

$tcp_worker->onWorkerStart = function($worker)
{
	// 定时，每10秒一次
	Timer::add(10, function()use($worker)
     	{
     		foreach ($worker->connectionsID as $key => $value) {
     			# code...
     			echo "online user :$key\n";
     		}
    	 });
};

//当客户端连接时
$tcp_worker->onConnect = function($connection)
{
	global $tcp_worker;
    	echo "new connection from ip " . $connection->getRemoteIp() . "\n";
    //	$connection->send(json_encode(array("msg" => "hi,sb")));
    	// $index = $tcp_worker->id . $connection->id;
    	// $connection->id = $index;
    	// $connection->send("id : $index\n");
};

// 当客户端发来数据时
$tcp_worker->onMessage = function($connection, $data) use ($tcp_worker)
{
	//global $tcp_worker;
	$data=str_replace("\r\n", "",$data);
	echo "$data";

	$returnData;
	/*$decode = explode("|", $data);
	$decode =str_replace("\r\n", "", $decode);*/
	$jsonData=json_decode($data,true);
	if(!isset($jsonData["action"])){
		$errormsg=array(
					"code"=>414,
					"data" => array("msg"=>"error msg type")
					);
		//echo "from ". $connection->getRemoteIp()."\n";
		$connection->send(json_encode($errormsg));
		sleep(5);
		if($connection->send(json_encode($errormsg)))
			echo "send succeed\n";

		return ;
	}
	switch ($jsonData["action"]) {


		//获取POI信息
		//GetPOI|palce&location
		case 'GetPOI':
			# code...


			$returnData =  getPOI::getPOIData($decode[1]);
	    		$connection->send($returnData);
			break;

		//登录
		//Login|account&password
		case 'Login':

			$userData=$jsonData["data"][0];
			//var_dump($userData);
			$returnData = user::login($userData);
			if($returnData["result"]=="OK"){
				if(!isset($connection->uid))
					$connection->uid = $userData["account"] ;
				$tcp_worker->connectionsID[$connection->uid] = $connection;

				//获取该用户的离线消息

			}
			$connection->send(json_encode($returnData));
			break;


		//登出
		//Logout
		case 'Logout':

			$userData = $jsonData["data"][0];
			//var_dump($userData[0]);

			if($connection===$tcp_worker->connectionsID[$userData["account"]]){
				$returnData = user::logout($userData);
				if($returnData["result"]=="OK"){
					unset($tcp_worker->connectionsID[$userData["account"]]);
				}


			}else{
				$returnData=array("result"=>"Fail",

							);
			}

			$connection->send(json_encode($returnData));
			break;

		//注册
		case 'Signup':
			$userData = $jsonData["data"][0];
			$returnData = user::signIn($userData);


			$connection->send(json_encode($returnData));
			break;

		//发送消息
		//Send|reciverName&message
		case 'Send':
			# code...

			$msg = $jsonData["data"][0];
			//var_dump($msg);
			if(sendMessageByUid($msg))
				$connection->send("send succeed\n");
			else
				$connection->send('send failed\n');
			break;

		default:
			$errormsg=array("code"=>444,
					"data" => array("msg"=>"unknown msg type"));
			$connection->send(json_encode($errormsg));
		
			sleep(5);
			$connection->send(json_encode($errormsg));
			break;

	}

};

//当客户端连接错误是
$tcp_worker->onError = function($connection, $code, $msg)
{
    echo "$connection error $code $msg\n";
};

$tcp_worker->onClose = function($connection) use($tcp_worker)
{
	echo "connection   closed\n";
	foreach ($tcp_worker->connectionsID as $key=>$value) {
		# code...
		if($value==$connection){
			unset($tcp_worker->connectionsID[$key]);
			echo "connection with $key closed\n";
		}

	}

};
//当worker停止时
$tcp_worker->onWorkerStop = function($worker)
{
    echo "Worker  $worker->id stopping...\n";
};

//通过connectionID发送消息
function sendMessageByUid($msg)
{
	global $tcp_worker;
	$sender=$msg["sender"];
	$receiver=$msg["receiver"];
	$msginfo=$msg["msginfo"];
	$newmsg=array("sender"=>$sender,
			"msginfo"=>$msginfo);
	var_dump($newmsg);
	if(isset($tcp_worker->connectionsID[$receiver]))
	{
	        	$connection = $tcp_worker->connectionsID[$receiver];
	        	$connection->send(json_encode($newmsg));
	        	return true;
    	}else{
    		//发送离线消息

    		return false;
    	}
}




// 运行所有worker实例
Worker::runAll();


?>