<?php
require_once "base.php";
require_once "library.php";

$user_in_list=false;
if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
	array_push($response["message"],"Username or password is empty");
	$response["status"]=502;
	$failed=true;
}else {
	include_once "passwd.php";
	foreach($password_list as $key => $password) {
		if(strcmp($key,$_SERVER['PHP_AUTH_USER'])==0)
		{
			$user_in_list=true;
			if(strcmp($password,$_SERVER['PHP_AUTH_PW'])!=0){
				array_push($response["message"],"Password is wrong");
				$response["status"]=503;
				$failed=true;
			}
		}
	}
	if($user_in_list==false){
		$array_push($response["message"],"User is not defined");
		$response["status"]=504;
		$failed=true;
	}
}
?>