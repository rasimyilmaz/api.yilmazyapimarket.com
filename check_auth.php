<?php
require_once "partials/base.php";
require_once "partials/security.php";
if ($failed==false){
    $response["status"]=200;
    $response["access_grant"]=true;
    array_push($response["message"],"Access Grant");
}else {
    $response["access_grant"]=false;
}
send_response($response);
?>