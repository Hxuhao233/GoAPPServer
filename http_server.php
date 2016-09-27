<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
require_once './Workerman/Autoloader.php';
require_once './user.class.php';
require_once './getPOI.php';

/**
 * 要不要写成一个类呢= =
 */
	

	// 创建一个Worker监听2347端口，不使用任何应用层协议
	$tcp_worker = new Worker("http://0.0.0.0:2347");

	//创建管理用户链接的数组
	$tcp_worker->connectionsID = array();

	// 启动1个进程对外提供服务
	$tcp_worker->count = 1;

	// $tcp_worker->onWorkerStart = function($worker)
	// {
	//     // 定时，每10秒一次
	//     Timer::add(10, function()use($worker)
	//     {
	//         // 遍历当前进程所有的客户端连接，发送当前服务器的时间
	//         foreach($worker->connections as $connection)
	//         {
	//         	//echo "send time to $connection->id\n";

	//             	//$connection->send(time());
	//         }
	//     });
	// };	

	// 当客户端发来数据时
	$tcp_worker->onMessage = function($connection, $data) use ($tcp_worker)
	{
		//global $tcp_worker;
		/*foreach ($data["post"] as $key => $value) {
		 	# code...
		 	var_dump($key);
		 	var_dump(json_decode($key));
		 } */
		 $tempData=array_keys($data["post"]);
		 $jsonData=json_decode($tempData[0],true);
		 var_dump($jsonData);
		
		//$connection->send(json_encode(array("name"=>"sb")));
		//var_dump($_POST["action"]);
		//$decode=array();
		//$decode = json_decode($_POST,true);
		//var_dump($decode);
		switch ($jsonData["action"]) {

			//获取POI信息
			//GetPOI|palce&location
			case 'GetPOI':
				# code...
				$returnData =  getPOI::getPOIData($jsonData[1]);	    	
		    		$connection->send($returnData);
				break;
			
			//登录
			//Login|account&password	
			case "Login":
				#
				$userData[0]=$jsonData["name"];
				$userData[1]=$jsonData["password"];
				//var_dump($userData);
				if($name = user::login($userData)){
					if(!isset($connection->uid))
						$connection->uid = $name ;
					$tcp_worker->connectionsID[$connection->uid] = $connection;		
					$connection->send("login succeed , your name is $connection->uid\n");
				}else{
					$connection->send("login failed\n");
				}
				break;
		
			
			//登出
			//Logout
			case 'Logout':
				$name = $decode[1];
				if(user::logout($name)){
					unset($tcp_worker->connectionsID[$name]);
					$connection->send("logout succeed\n");
				}else{
					$connection->send("logout fail\n");
				}
				break;



			//发送消息
			//Send|reciverName&message
			case 'Send':
				# code...
			
				$msg = explode('&',$decode[1]);
				if(sendMessageByUid($msg[0],$msg[1]))
					$connection->send("send succeed\n");
				else
					$connection->send('send failed\n');
				break;
				
		}/**/

	};

	//当客户端连接错误是
	$tcp_worker->onError = function($connection, $code, $msg)
	{
	    echo "$connection error $code $msg\n";
	};

	//当worker停止时
	$tcp_worker->onWorkerStop = function($worker)
	{
	    echo "Worker  $worker->id stopping...\n";
	};

	//通过connectionID发送消息
	function sendMessageByUid($id, $message)
	{
		global $tcp_worker;	
		if(isset($tcp_worker->connectionsID[$id]))
		{
	        	$connection = $tcp_worker->connectionsID[$id];
	        	$connection->send($message);
	        	return true;
	    	}else{
	    		return false;
	    	}
	}
	// 运行所有worker实例
	Worker::runAll();

?>