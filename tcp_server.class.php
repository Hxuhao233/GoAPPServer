<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
require_once './Workerman/Autoloader.php';
require_once './user.class.php';
require_once './getPOI.php';

class tcp_server extends Worker{
	static private $ClientConnection = array();


	public function _construct(){
		$this->Worker("tcp://0.0.0.0:6666");
		$this->count = 4;
	}
/*	// 创建一个Worker监听2347端口，不使用任何应用层协议
	$tcp_worker = new Worker("tcp://0.0.0.0:2347");

	// 启动4个进程对外提供服务
	$tcp_worker->count = 4;

	$tcp_worker->onWorkerStart = function($worker)
	{
	    // 定时，每10秒一次
	    Timer::add(10, function()use($worker)
	    {
	        // 遍历当前进程所有的客户端连接，发送当前服务器的时间
	        foreach($worker->connections as $connection)
	        {
	        	echo "send time to $connection->id\n";

	            	$connection->send(time());
	        }
	    });
	};	

	//当客户端连接时
	$tcp_worker->onConnect = function($connection)
	{
	    	echo "new connection from ip " . $connection->getRemoteIp() . "\n";  

	};

	// 当客户端发来数据时
	$tcp_worker->onMessage = function($connection, $data)
	{
		// var_dump($data);
		$userName=login(array('root','root'));
		if(is_null($userName)){
			echo 'not found';
		}else{
			echo 'Hello'.$userName;
		}
		$returnData =  getPOI::getPOIData($data);
	    	// 向客户端发送hello $data
	    	$connection->send($returnData);
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
	};*/
	// 运行所有worker实例
	

}
$server = new tcp_server(6666);
Worker::runAll();
?>