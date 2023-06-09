<?php
//xdebug configuration
//https://www.linkedin.com/pulse/debugging-php-code-remote-serveronly-accessible-vpn-using-stewart
require_once "partials/base.php";
require_once "partials/library.php";
// Takes raw data from the request
$post = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($post);
require_once "security.php";
require_once "restrictions.php";
require_once "functions.php";
if ($failed == false){
	try{
        $day=15;
		require_once 'partials/db_connect.php';
		$query=get_debtaccountinfo_query($day);
		$query = $db->query($query, PDO::FETCH_ASSOC);
		$products = array();
		$i=0;
		foreach( $query as $row ){
			$i+=1;
			$item = array();
			$item["code"]=$row["code"];
			$item["fullname"] = $row["fullname"];
			$item["city"] = $row["city"];
			$item["phone_number"] = $row["phone_number"];
			$item["balance"] = $row["balance"];
			$item["day"] = $row["day"];
			array_push($list, $item);
		}
		if($i==0){
			$array_push($response["message"],"Kriterlerle eşleşen ürün bulunamadı");
			$response["status"]=401;
		} else {
			$response["list"]=$list;
			array_push($response["message"],strval($i)." adet cari kayıt bulundu");
			$response["status"]=200;
		}
	}
	catch ( PDOException $e ){
		array_push($response["message"],$e->getMessage());
		$response["status"]=504;
	}
}
send_response($response);
$response=null;
?>
