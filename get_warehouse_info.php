<?php
require_once 'partials/base.php';
require_once 'partials/library.php';
require_once 'partials/definations.php';
require_once 'partials/security.php';
require_once 'partials/restrictions.php';
if($failed==false){
    $response["status"]=200;
    $response["revision"]=$warehouse_info->revision;
    $response["collection"]=$warehouse_info->collection;
    array_push($response["message"],"Call received");
    send_response($response);
}else {
    send_response($response);
}
$response=null;
?>