<?php
if(!isset($_GET["Kod"]) or date("G")<8)
{
	$response["success"] = 0;
    $response["message"] = "Missing parameter";
	echo json_encode($response);
	return;
}
$Kod=$_GET["Kod"];
$response=array();
$response["actions"] = array();
if(isset($_GET["Ayd"]))
{
	require_once __DIR__ . '/db_connect.php';
	$query = $db->query("SELECT TOP 20 * FROM ActionView where GKod='$Kod' order by Tarih desc", PDO::FETCH_ASSOC);
	if ( $query->rowCount() )
	{
		global $response;
		foreach( $query as $row )
		{
		    $action= array();
            $action["GFiyat"] = $row["GFiyat"];
            $action["GMiktar"] = $row["GMiktar"];
            $action["GSatici"] = $row["GSatici"];
            $action["GTarih"] = $row["GTarih"];
        	array_push($response["actions"], $action);
     	}
    $response["success"] = 1;
    $db=null;
	}
	require_once __DIR__ . '/db_connectAyd2.php';
	$query = $db->query("SELECT TOP 20 * FROM ActionView where GKod='$Kod' order by Tarih desc", PDO::FETCH_ASSOC);
	if ( $query->rowCount() )
	{
		global $response;
		foreach( $query as $row )
		{
		    $action= array();
            $action["GFiyat"] = $row["GFiyat"];
            $action["GMiktar"] = $row["GMiktar"];
            $action["GSatici"] = $row["GSatici"];
            $action["GTarih"] = $row["GTarih"];
        	array_push($response["actions"], $action);
     	}
    $response["success"] = 1;
    $db=null;
	}
}
if ( count($response["actions"])==0){
	global $response;
	unset($response["actions"]);
	$response["success"] = 0;
    $response["message"] = "No action found";
}
echo json_encode($response);
$response=null;
?>
