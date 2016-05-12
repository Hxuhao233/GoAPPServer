<?php

/**
 * mysql操作类
 * 基本数据库操作能完成，然而并不完善，所以有时要自己写完整的sql语句
 */
class mysqlHandler
{
	private $table = null;
	private $link = null;
	private $result = null;
/*
	public function __set($name , $value){
	        $this->values[$name] = $value;
	}

	public function __get($name){
		if(isset($this->values[$name])){
	            return $this->values[$name];
	        }
	        else{
	            return null;
	        }
	 }
*/
	public function __construct($db,$table){
		$this->link = new mysqli("127.0.0.1", "root", "hxh896900488", "$db");
		$this->link->query("SET NAMES utf8");
		$this->table = $table;
	}
	public function __destory(){
		if(!is_null($this->link))
			$this->link->close();
		if(!is_null($this->result))
			$this->result->free();
	}


	public function getLink(){

		return $this->link;
	}


	private function clear($data){
		return $this->link->real_escape_string($data);
	}

	private function excute($query){
		//$this->result = $this->link->query($query);
		//return $this->result;
		echo $query;
		return $this->link->query($query);
	}

	public function select($column = "*" , $conditions = array()){

		$sql = "SELECT $column FROM `$this->table` ";
		$qualifier ="";
		foreach($conditions as $key => $value){
			if(!empty($qualifier)){
			        $qualifier .= ' AND ';
			}
			if($key=="password")
		         	$qualifier .= "`$key`= MD5(\"" . $this->clear($value) . "\")";
		        else
		            	$qualifier .= "`$key`= \"" . $this->clear($value) . "\" ";
		 }
		//echo $qualifier;
		$sql .=  $qualifier ? "WHERE $qualifier " :null;
		//echo $sql;
		return $this->excute($sql);
	}


	public function update($data=array() ,$condition=array()){
		$updateData = "";
		//$data = $this->link->real_escape_string($data);
		foreach($data as $key => $value){
			if($key=="password")
				$updateData .= "`".$this->clear($key) ."` = MD5(\""  .$this->clear($value)."\" ),";
			else
				$updateData .= "`".$this->clear($key) ."` = \""  .$this->clear($value)."\" ,";
		}
		$updateData = substr($updateData, 0, strlen($updateData)-1);

		$qualifier ="";
		foreach($condition as $column => $value){
			if(!empty($qualifier)){
			         $qualifier .= ' AND ';
			}
			if($column=="password")
		         	$qualifier .= "`$column`= MD5(\"" . $this->clear($value) . "\")";
		        else
		            	$qualifier .= "`$column`= \"" . $this->clear($value) . "\" ";
		 }


		$sql = "UPDATE `$this->table` SET $updateData ";
		$sql .=  $qualifier ? "WHERE $qualifier " :null;
	//	echo $sql;

		return $this->excute($sql);
	}

	public function delete($id){

		$sql= "DELETE FROM `$this->table`  WHERE `id` = $id";

		return $this->excute($sql);
	}

	public function insert($data=array()){

		$colNames = " ";
		$colValues = " ";
		foreach($data as $key => $value){
			$colNames .='`' .$this->clear($key).'`,';
			if($key == "password")
				$colValues .= "MD5(\"".$this->clear($value)."\"),";
			else
				$colValues .= "\"".$this->clear($value)."\",";
		}
		$colNames = substr($colNames, 0, strlen($colNames) - 1);
         		$colValues = substr($colValues, 0, strlen($colValues) - 1);

		$sql="INSERT INTO `$this->table` (".$colNames.") VALUES (".$colValues .')';
		//echo $sql;

		return $this->excute($sql);
	}


}



?>