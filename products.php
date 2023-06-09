<?php
//xdebug configuration
//https://www.linkedin.com/pulse/debugging-php-code-remote-serveronly-accessible-vpn-using-stewart
require_once "partials/base.php";
require_once "partials/library.php";
// Takes raw data from the request
$post = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($post);
require_once "partials/security.php";
require_once "partials/restrictions.php";
require_once "partials/functions.php";

$flag_product_code=$data->{'is_code_filled'};
$flag_product_barcode=$data->{'is_barcode_filled'};
$flag_product_name=$data->{'is_name_filled'};
$flag_product_price=$data->{'is_price_filled'};

if(!($flag_product_name or $flag_product_code or $flag_product_barcode or $flag_product_price))
{
	array_push($response["message"],"Yetersiz Bilgi");
	$response["status"]=505;
	$failed=true;
}

if ($failed == false){
	try{
		require_once 'partials/db_connect.php';
		$query=build_query($data,$flag_product_code,$flag_product_barcode,$flag_product_price);
		$stmt=$db->prepare($query,[PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
		if($flag_product_price){
			$stmt->bindValue(':price' , $data->{'product_price'});
		}
		$stmt->execute();
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$products = array();
		$i=0;
		foreach( $rows as $row ){
			$i+=1;
			$product = array();
			$product["code"]=$row["code"];
			$product["barcode"] = $row["barcode"];
			$product["name"] = $row["name"];
			$product["short_name"] = $row["short_name"];
			$product["price"] = $row["price"];
			$product["currency_name"] = $row["currency_name"];
			$product["warehouse_quantity_collection"]=array();
			foreach($warehouse_info->collection as $warehouse){
				$point=array();
				$point["id"]=$warehouse->id;
				$point["quantity"]=$row["warehouse_".$warehouse->id];
				array_push($product["warehouse_quantity_collection"],$point);
				unset($point);
			}
			$product["unit"] = $row["unit"];
			$product["price_change_date"]=$row["price_change_date"];
			$product["price_change_user"]=$row["price_change_user"];
			$product["local_currency_price"]=$row["local_currency_price"];
			array_push($products, $product);
		}
		if($i==0){
			$array_push($response["message"],"Kriterlerle eşleşen ürün bulunamadı");
			$response["status"]=401;
		} else {
			$response["products"]=$products;
			array_push($response["message"],strval($i)." adet ürün bulundu");
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
