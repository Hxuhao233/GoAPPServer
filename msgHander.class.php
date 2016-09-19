<?php
require_once './mysql/mysql.class.php';
class msgHandler{
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
?>