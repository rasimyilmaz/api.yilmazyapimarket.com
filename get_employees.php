<?php
header('Content-Type: application/json; charset=utf-8');
$post = file_get_contents('php://input');
$data = json_decode($post);
date_default_timezone_set('Europe/Istanbul');
if(date("G")<8)
{
	$response["success"] = 0;
    $response["message"] = "This is not working time period!";
	echo json_encode($response);
	return;
}
$response=array();
require_once 'partials/db_connect.php';
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try 
{
	$query = $db->query("select cari_per_adi +' ' + cari_per_soyadi as Employee from CARI_PERSONEL_TANIMLARI where cari_per_tip<>3 order by cari_per_adi +' ' + cari_per_soyadi ", PDO::FETCH_ASSOC);
	if ( $query->rowCount() )
	{
		global $response;
		$response["Employees"] = array();
		$response["success"] = 1;
		foreach( $query as $row )
			{
				$Employee = array();
				$Employee["name"]=$row["Employee"];
				array_push($response["Employees"],$Employee);
			}
	}	
else
	{
		$response["success"] = 0;
		$response["message"] = "No employee found";
	}
}
catch ( PDOException $e )
{
		$response["success"] = 0;
		$response["message"] = $e->getMessage();
}
$db=null;
echo json_encode($response);
$response=null;
?>
