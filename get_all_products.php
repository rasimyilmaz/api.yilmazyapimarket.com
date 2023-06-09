<?php
//https://stackoverflow.com/questions/4064444/returning-json-from-a-php-script
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Istanbul');
// Takes raw data from the request
$post = file_get_contents('php://input');
$data = json_decode($post);
if(!isset($data->{'ara'}) or date("G")<8)
{
	$response["success"] = 0;
    $response["message"] = "No products found :".$data->{'ara'};
	echo json_encode($response);
	return;
}
$ara=trim($data->{'ara'});
$response=array();
$response["products"] = array();
$filter="";
$dilimler = explode(" ", $ara);

for ($i=0;$i<count($dilimler);$i++){
	if($i>0)
	{
		$filter.=" and ";
	}
	$filter.="isim like '%$dilimler[$i]%'";
}
require_once 'partials/db_connect.php';
$query = $db->query("SELECT TOP 49 * FROM StokView2 where ($filter) or Barkod='$ara'", PDO::FETCH_ASSOC);
if($query->rowCount())
{
	global $response;
	foreach( $query as $row ){
		$product = array();
		$product["Kod"]=$row["Kod"];
		$product["isim"] = $row["isim"];
		$product["Barkod"] = $row["Barkod"];
		$product["Fiyat"] = $row["Fiyat"];
		$product["Doviz"] = $row["Doviz"];
		$product["Miktar"] = $row["Miktar"];
		$product["Birim"] = $row["Birim"];
		$product["Konum"]="Ayd";
		array_push($response["products"], $product);
	}
$response["success"] = 1;
$db=null;
}
if ( count($response["products"])==0){
	global $response;
	unset($response["products"]);
	$response["success"] = 0;
    $response["message"] = "No product found";
}
echo json_encode($response);
$response=null;
?>
