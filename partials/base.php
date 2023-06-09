<?php 
//https://stackoverflow.com/questions/4064444/returning-json-from-a-php-script
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Istanbul');
$response=array();
$response["message"] =array();
$failed=false;
?>