<?php
require_once("./mysql.class.php");
$mysqli_test = new mysqlHandler('GoAPP','User');

$condition = array(
					'name' => 'user666',
					'status' => '1'
					);
$result = $mysqli_test->select(" *", $condition);
if(!empty($result)){
	$data = $result->fetch_assoc();
	var_dump($data);
}else{
	echo 'fail';
}
/*
$insertData = array('name' => "Hxuhao",
					'password' => "123"
					);
if(!$mysqli_test->insert($insertData)){
	echo 'fail';
}
/*


$updateData = array(
					'status' => '1',
					'password' => "12345"
					);

$condition = array(
					'name' => 'user666',
					'status' => '0'
					);
if(!$mysqli_test->update($updateData,$condition)){
	echo 'fail';
}
*/
?>