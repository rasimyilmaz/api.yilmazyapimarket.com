<?php
require_once "base.php";
require_once "library.php";
if(date("G")<8)
{
    array_push($response["message"],"Sabah 8 den önce işlem almıyoruz");
    $response["status"]=506;
    $failed=true;
}
?>