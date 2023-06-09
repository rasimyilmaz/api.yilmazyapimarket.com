<?php
header('Content-Type: application/json; charset=utf-8');
$post = file_get_contents('php://input');
$data = json_decode($post);
date_default_timezone_set('Europe/Istanbul');
$Company="Ayd";
if(date("G")<8)
{
	$response["success"] = 0;
    $response["message"] = "This is not working time period!";
	echo json_encode($response);
	return;
}
if(!isset($data->{"Invoiceid"}) or !isset($data->{"Recordid"}) or !isset($data->{"Amount"}) or !isset($data->{"Person"})  )
{
	$response["success"] = 0;
    $response["message"] = "Some of parameters is missing!";
	echo json_encode($response);
	return;
}
$Line1Number="";
if(isset($data->{"Line1Number"}))
{
	$Line1Number=$data->{"Line1Number"};
}
$DeviceId="";
if(isset($data->{"DeviceId"}))
{
	$DeviceId=$data->{"DeviceId"};
}
$Invoiceid=(int)substr(trim($data->{"Invoiceid"}),0,7);
$Recordid=$data->{"Recordid"};
$Amount=$data->{"Amount"};
$Person=$data->{"Person"};
$response=array();
if ($Invoiceid > 670020) {
	require_once 'partials/db_connect.php';
} elseif ($Invoiceid > 467593) {
	require_once 'partials/db_connect_2022.php';
} else {
	require_once 'partials/db_connect_2020.php';
}
$response["success"] = 0;
$count=0;
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try
{	
	$query="declare @cha_Guid uniqueidentifier;declare @number float;SELECT @cha_Guid=cha_Guid FROM [dbo].[CARI_HESAP_HAREKETLERI] where cha_evrak_tip=63 and cha_SpecRecNo='$Invoiceid';";
	$query.="with cte as (Select Recordid  , sum(Amount) as TotalAmount from RMS.dbo.Delivery where Invoiceid =@cha_Guid and Company='".$Company."' group by Recordid) ";
	$query.="SELECT @number=(sth_miktar-isnull(TotalAmount,0)) FROM [dbo].[STOK_HAREKETLERI] left outer join cte on cte.Recordid= sth_Guid where sth_fat_uid =@cha_Guid and sth_Guid='$Recordid' ";
	$query.="if (@number>=$Amount) begin insert into Rms.dbo.Delivery(Company,Invoiceid,Recordid,Amount,recordTime,Person,Line1Number,DeviceId) ";
	$query.="values('$Company',@cha_Guid,'$Recordid','$Amount',GETDATE(),'$Person','$Line1Number','$DeviceId') end";
	$count = $db->exec($query);
	if ($count>0)
	{
		$response["success"] = 1;
	}else
	{
		$response["message"] =$query;
	}
}
catch ( PDOException $e )
{
	$response["message"] = $e->getMessage();
}
$db=null;
echo json_encode($response);
$response=null;
?>
