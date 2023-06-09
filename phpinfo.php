<?php
include_once "partials/base.php";
include_once "partials/library.php";
include_once "partials/security_basic_auth.php";
if(!$failed){
    phpinfo();
}else {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    send_response($response);
    exit;
}
?>