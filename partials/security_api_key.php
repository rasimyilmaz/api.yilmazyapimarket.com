<?php
require_once "base.php";
require_once "library.php";
require_once "definations.php";

if(!isset($_SERVER["HTTP_X_API_KEY"])){
	array_push($response["message"],"Api key is missing");
	$response["status"]=501;
	$failed=true;
}else{
	if(strcmp($_SERVER["HTTP_X_API_KEY"],API_KEY)!=0){
		array_push($response["message"],"Api key is wrong");
		$response["status"]=502;
		$failed=true;
	}
}
?>