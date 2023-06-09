<?php
$company="Ayd";
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
if(!isset($data->{"Invoiceid"}))
{
	$response["success"] = 0;
    $response["message"] = "Invoice id is not declared!";
	echo json_encode($response);
	return;
}
$id=trim($data->{"Invoiceid"});
if (strlen($id)!=8)
{
	$response["success"] = 0;
    $response["message"] = "Invoice id is not true!";
	echo json_encode($response);
	return;
}
$ara=(int)substr(trim($data->{"Invoiceid"}),0,7);
$response=array();
$cha_Guid="";

if ($ara > 670020) {
	require_once __DIR__ . '/db_connect.php';
} elseif ($ara > 467593) {
	require_once __DIR__ . '/db_connect_2022.php';
} else {
	require_once __DIR__ . '/db_connect_2020.php';
}

$query = $db->query("SELECT cast (cha_Guid as nvarchar(50)) as cha_Guid, dbo.fn_SubeAdi(cha_firmano,cha_subeno) as Sube,
[cha_belge_no] as Documentid,case when len(cari_vdaire_no )!=11  then cari_unvan1 + ' ' + cari_unvan2  else cari_unvan2+ ' ' +cari_unvan1 end as Unvan,
case when [cha_tpoz]=0 then 'Açık' else 'Kapalı' end as Durum,convert(nvarchar(40),cast ([cha_meblag] as money),1) Tutar,
convert(nvarchar(40),[cha_tarihi],104) as Tarih,dbo.fn_KullaniciUzunAdi(cha_create_user) as Kullanici,
case when dbo.fn_CariHesapAnaDovizBakiye('', 0, cari_kod, '', '', 0, NULL, NULL, 0,0,0,0,0) >= 0 then 'Borcu' else 'Alacağı' end +' : '+ convert(varchar(30), cast(abs(dbo.fn_CariHesapAnaDovizBakiye('', 0, cari_kod, '', '', 0, NULL, NULL, 0,0,0,0,0)) as money), 1) as Bakiye
FROM [dbo].[CARI_HESAP_HAREKETLERI]
left outer join CARI_HESAPLAR on cha_ciro_cari_kodu =cari_kod
where cha_evrak_tip=63 and cha_SpecRecNo='$ara'", PDO::FETCH_ASSOC);
if ( $query->rowCount() )
{
    global $cha_Guid;
	global $response;
	foreach( $query as $row )
		{
			$Header=array();
			$cha_Guid=$row["cha_Guid"];
			$Header["Sube"]=$row["Sube"];
			$Header["Documentid"] = $row["Documentid"];
			$Header["Unvan"] = $row["Unvan"];
			$Header["Durum"] = $row["Durum"];
			$Header["Tutar"] = $row["Tutar"];
			$Header["Tarih"] = $row["Tarih"];
			$Header["Kullanici"] = $row["Kullanici"];
			$Header["Bakiye"]=$row["Bakiye"];
			$response["Header"]=$Header;
		}
	$linequery = $db->query("with cte as (Select cast(Recordid as nvarchar(50)) as Recordid, sum(Amount) as TotalAmount from RMS.dbo.Delivery 
	where Invoiceid ='$cha_Guid' and Company='$company' group by Recordid) 
	SELECT cast([sth_Guid] as nvarchar(50)) as Recordid,dbo.fn_DepoIsmi(sth_cikis_depo_no) as Depo,[sth_stok_kod] as Kod
	,dbo.fn_StokIsmi(sth_stok_kod) as isim,[sth_miktar] as Miktar,dbo.fn_StokBirimi(sth_stok_kod,sth_birim_pntr) as Birim
	,convert(nvarchar(30),cast(([sth_tutar]-([sth_iskonto1]+[sth_iskonto2]+[sth_iskonto3]+[sth_iskonto4]+[sth_iskonto5]+[sth_iskonto6])+[sth_vergi])/[sth_miktar] as money),1) as Fiyat
	,isnull(TotalAmount,0) as given , sth_miktar-isnull(TotalAmount,0) as remain 
	FROM [dbo].[STOK_HAREKETLERI] left outer join cte on cte.Recordid= sth_Guid
    where sth_fat_uid ='$cha_Guid'", PDO::FETCH_ASSOC);
	if ( $linequery->rowCount() )
	{
		global $response;
		$response["Lines"] = array();
		$NotAllOfThemGiven=false;
		foreach( $linequery as $linerow )
			{
			    $Line = array();
				$Line["Recordid"]=$linerow["Recordid"];
        	    $Line["Depo"] = $linerow["Depo"];
        	    $Line["Kod"] = $linerow["Kod"];
				$Line["isim"]=$linerow["isim"];
        	    $Line["Miktar"] = $linerow["Miktar"];
        	    $Line["Birim"] = $linerow["Birim"];
        	    $Line["Fiyat"] = $linerow["Fiyat"];
    		    $Line["given"] = $linerow["given"];
        	    $Line["remain"] = $linerow["remain"];
				if ($linerow["remain"]>0)
				{
					$NotAllOfThemGiven=true;
				}
        		array_push($response["Lines"], $Line);
     		}
		if($NotAllOfThemGiven)
		{
			$response["message"]="Henüz ürünlerin tamamı teslim edilmedi.";
		}
		else
		{
			$response["message"]="Dikkat !\nBütün ürünler teslim edildi.\nÜrün teslim edilemez!";
		}

		$response["success"] = 1;
	}
	else
	{
		global $response;
		$response["success"] = 0;
		$response["message"] = "Faturayla ilgili hata var, Rasim Beye haber veriniz.";
	}
	$deliveryquery = $db->query("SELECT id,convert(nvarchar(10),RecordTime,104) +' ' + convert(nvarchar(5),RecordTime,108) as RecordTime,Person,Amount ,dbo.fn_StokBirimi(sth_stok_kod,sth_birim_pntr) as Birim,
	[sth_stok_kod] as Kod,dbo.fn_StokIsmi(sth_stok_kod) as isim FROM [dbo].[STOK_HAREKETLERI] right join RMS.dbo.Delivery on Recordid= sth_Guid 
	where sth_fat_uid ='$cha_Guid' and Company='$company' and Invoiceid='$cha_Guid' order by id Desc", PDO::FETCH_ASSOC);
	if ( $deliveryquery->rowCount() )
	{
		global $response;
		$response["Deliveries"] = array();
		foreach( $deliveryquery as $delrow )
			{
			    $Delivery = array();
				$Delivery["id"]=$delrow["id"];
        	    $Delivery["RecordTime"] = $delrow["RecordTime"];
				$Delivery["Person"]=$delrow["Person"];
				$Delivery["Amount"]=$delrow["Amount"];
        	    $Delivery["Birim"] = $delrow["Birim"];
        	    $Delivery["Kod"] = $delrow["Kod"];
        	    $Delivery["isim"] = $delrow["isim"];
        		array_push($response["Deliveries"], $Delivery);
     		}
	}
	else
	{
		global $response;
		if ($response["success"])
		{
			$response["message"] = "Hiç teslimat yapılmadı.";
		}
		else
		{
			$response["message"] = "Teslimat hiç teslimat yapılmamış.\nFaturada teknik sorun var Rasim beye haber verin.";	
		}
	}
}
else
{
		$response["success"] = 0;
		$response["message"] = "Firma seçimin yanlış olabilir.";
}
$db=null;
echo json_encode($response,JSON_THROW_ON_ERROR);
$response=null;
?>
