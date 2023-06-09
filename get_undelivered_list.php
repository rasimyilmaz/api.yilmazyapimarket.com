<html>
<head>
<meta http-equiv="refresh" content="300">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
	$('.header').toggleClass('expand').nextUntil('tr.header').slideToggle(100);
	$('.header').click(function(){
		$(this).toggleClass('expand').nextUntil('tr.header').slideToggle(100);
	});
});
function MakeDelivery(Company,Invoiceid,Recordid,Amount)
{
	DisableButton(Recordid,true);
	var Person= document.getElementById("Person").value;
	var Url = "insert_delivery.php?Company="+Company+"&Invoiceid="+Invoiceid+"&Recordid="+Recordid+"&Amount="+Amount+"&Person="+Person.replace(/ /g, "%20");
	httpGetAsync(Url,ContinueMakeDelivery,Recordid);
}
function ContinueMakeDelivery(response,Recordid){
	var obj=JSON.parse(response);
	if ( obj.success===1){
		HideRow(Recordid);
	}else{
		DisableButton(Recordid,false);
	}

}
function HideRow(Recordid){
	document.getElementById(Recordid+"_Message").innerHTML = '<p class="ok">Teslimat İşlendi.</p>';
}
function DisableButton(Recordid,Status){
	document.getElementById(Recordid).disabled = Status;
}
function httpGetAsync(theUrl, callback,Recordid)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200)
            callback(xmlHttp.responseText,Recordid);
    };
    xmlHttp.open("GET", theUrl, true); // true for asynchronous 
    xmlHttp.send(null);
}
</script>
<style>
table,
tr,
td,
th {
  border: 1px solid black;
  border-collapse: collapse;
}
p.ok{
	color :green;
}
tr.header {
  cursor: pointer;
}
.header .sign:after {
  content: "+";
  display: inline-block;
}
.header.expand .sign:after {
  content: "-";
}
td.center{
	text-align:center;
}
td.right{
	text-align:right;
}
.detail.odd{
	background-color:#FFFFFF;
}
.detail.even{
	background-color:#FCFBE3;
}
.header.odd{
	color:#444;
	background-color:#F7FDFA;
}
.header.even{
	color:#444;
	background-color:#D2E4FC;
	vertical-align:top
}
.tg  {border-collapse:collapse;border-spacing:0;border-color:#999;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;color:#fff;background-color:#26ADE4;text-align:center;}
.th2{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;color:#fff;background-color:#26ADE4;}
@media screen and (max-width: 767px) {.tg {width: auto !important;}.tg col {width: auto !important;}.tg-wrap {overflow-x: auto;-webkit-overflow-scrolling: touch;}}
</style>
</head>
<body>
<?php
if(date("G")<8)
{
	$response["success"] = 0;
    $response["message"] = "This is not working time period!";
	echo json_encode($response);
	return;
}
$response=array();

if(isset($_GET["company"]))
{	
	$company=$_GET["company"];
	
	if($company=="Ayd")
	{
	require_once 'partials/db_connectAyd.php';
	}
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
function convertDate($date)
{
    $d = new DateTime(date($date));
    if ($d && $d->format('Y-m-d') == $date)
	{ 
		return $d;
	} else 
	{
		return null;
	}
}
$start_date=new DateTime(date('Y-m-d'));
$end_date=(new DateTime())->modify('-59 minute');
$input_start_date=convertDate($_GET["start_date"]);
$input_end_date=convertDate($_GET["end_date"]);
if(isset($input_start_date))
	{
		$start_date=$input_start_date;
	}
if (isset($input_end_date))
{
	$end_date=$input_end_date;
}
?>
<?php
$Query1_Text='declare @start_date datetime =Cast(\''.$start_date->format("Y-m-d").'\' as datetime2) declare @end_date datetime=cast(\''.$end_date->format("Y-m-d H:i").'\' as datetime2)
SELECT person as [İsim Soyisim] , count(distinct(d.Invoiceid)) as [Farklı Fatura Adedi], Sum(d.Amount) as [Teslim Edilen Toplam Ürün Adedi],convert(nvarchar(40),cast (sum(round(((s.sth_tutar-(s.sth_iskonto1+s.sth_iskonto2+s.sth_iskonto3+s.sth_iskonto4+s.sth_iskonto4+s.sth_iskonto5+s.sth_iskonto6)-s.sth_vergi)/sth_miktar)*d.Amount,2))as money),1) as [Teslimatın Toplam Tutarı],
convert(nvarchar(40),cast (round(sum(((s.sth_tutar-(s.sth_iskonto1+s.sth_iskonto2+s.sth_iskonto3+s.sth_iskonto4+s.sth_iskonto4+s.sth_iskonto5+s.sth_iskonto6)-s.sth_vergi)/sth_miktar)*d.Amount) / Sum(d.Amount) ,2 )as money),1) as [Teslim Edilen Ürünün Ortalama Birim Fiyatı]
FROM [RMS].[dbo].[Delivery] as  d left outer join [MikroDB_V15_YILMAZ16].dbo.STOK_HAREKETLERI  as s on
d.Recordid=s.sth_RECid_RECno 
where DATEDIFF(MINUTE,@start_date, RecordTime)>0  and DATEDIFF(MINUTE,RecordTime,@end_date)>0
group by person order by cast (sum(round(((s.sth_tutar-(s.sth_iskonto1+s.sth_iskonto2+s.sth_iskonto3+s.sth_iskonto4+s.sth_iskonto4+s.sth_iskonto5+s.sth_iskonto6)-s.sth_vergi)/sth_miktar)*d.Amount,2))as money)  Desc';
$Query1_success=false;
$Query1_errorMessage="";
try {
$Query1=$db->query($Query1_Text,PDO::FETCH_NUM);
$Query1_success=true;
}
catch (Exception $e )
{
	$Query1_errorMessage=$e->getMessage();
}
?>
<?php if($Query1_success): ?>
<div class="tg-wrap">
<table id="tg-2KEWD" class="tg">
<th class="center" colspan=5><?php echo 'Teslimat Çizelgesi '.$start_date->format("d.m.Y H:i").' ile '.$end_date->format("d.m.Y H:i").' tarihleri arası' ?></th>
  <tr class="th2"><td>İsim Soyisim </td><td>| Farklı Fatura Adedi </td><td>| Toplam Ürün Adedi </td><td>| Toplam Tutar </td><td>| Ürünlerin Ortalama Fiyatı</td></tr>
  <?php
  	$Query1_Counter=0;
  	foreach($Query1 as $row){
		  $Query1_Counter++;
		  echo '<tr class="detail ';
		  if ($Query1_Counter % 2 == 1 ){
			echo 'odd">';
			#echo "\n";
		  }else {echo 'even">'; }
		  echo '<td>'.$row[0].'</td>'.'<td class="center">'.$row[1].'</td>'.'<td class="center">'.round($row[2],0).'</td>';
		  echo '<td class="right">'.$row[3].'</td>'.'<td class="center">'.$row[4].'</td>';
		  echo '</tr>';
	  }
  ?>
</table>
</div>
<br />
<br />
<?php else : ?>
<h1> Teslimat Çizelgesi Oluşturulamadı.</h1>
<p><?php echo $Query1_errorMessage; ?></p>
<?php endif ?>
<?php 
$Query2_Text="select cari_per_adi +' ' + cari_per_soyadi as Employee from CARI_PERSONEL_TANIMLARI where cari_per_tip<>3 order by cari_per_adi +' ' + cari_per_soyadi ";
$Query2=$db->query($Query2_Text,PDO::FETCH_NUM);
?>
<div class="tg-wrap">
<table id="tg-2KEWD" class="tg">
<th colspan=3>Teslimatı Yapılmamış Görünen Fatura Listesi</th>
<th>
<?php
	echo '<select id="Person">';
	foreach($Query2 as $row)
	{
  		echo '<option value="'.$row[0].'">'.$row[0].'</option>';
	}
	echo '</select>';
?>
</th>
  <tr class="th2"><td>Fatura No</td><td>Ünvan</td><td>Tutar</td><td>Durum</td></tr>
  
<?php
try 
{
$listInvoiceIds = $db->query("select distinct (sth_fat_recid_recno) as InvoiceId 
from [dbo].STOK_HAREKETLERI as s inner join vw_Stok_Hareket_Evrak_Isimleri as i on  s.sth_evraktip=i.SHEvrNo 
where i.SHEvrIsim like 'Çıkış faturası' and datediff(minute ,'2017-02-24', sth_tarih)>=0 and sth_pos_satis=0",PDO::FETCH_ASSOC);
}
catch ( PDOException $e )
{
	print ' '.$e->getMessage();
}
$k=0;
foreach ($listInvoiceIds as $id)
{
$ara=$id["InvoiceId"];
$query = $db->query("SELECT dbo.fn_SubeAdi(cha_firmano,cha_subeno) as Sube,
[cha_belge_no] as Documentid,case when len(cari_vdaire_no )!=11  then cari_unvan1 + ' ' + cari_unvan2  else cari_unvan2+ ' ' +cari_unvan1 end as Unvan,
case when [cha_tpoz]=0 then 'Açık' else 'Kapalı' end as Durum,convert(nvarchar(40),cast ([cha_meblag] as money),1) Tutar,
convert(nvarchar(40),[cha_tarihi],104) as Tarih,dbo.fn_KullaniciUzunAdi(cha_create_user) as Kullanici,
case when dbo.fn_CariHesapAnaDovizBakiye('', 0,cha_ciro_cari_kodu , '','', NULL, NULL, NULL, 0) >= 0 then 'Borcu' else 'Alacağı' end +' : '+ convert(varchar(30), cast(abs(dbo.fn_CariHesapAnaDovizBakiye('', 0,cha_ciro_cari_kodu , '','', NULL, NULL, NULL, 0)) as money), 1) as Bakiye
FROM [dbo].[CARI_HESAP_HAREKETLERI]
left outer join CARI_HESAPLAR on cha_ciro_cari_kodu =cari_kod
where cha_evrak_tip=63 and cha_RECno='$ara'", PDO::FETCH_ASSOC);
if ( $query->rowCount() )
{
	$mainline="";
	foreach( $query as $row )
		{		
			$mainline=$mainline. '';
			$mainline=$mainline. '<td><span class="sign"></span>'.$row["Documentid"].' B:'.str_pad($ara, 7,"0", STR_PAD_LEFT).'0</td>';
			$mainline=$mainline. '<td>'.$row["Unvan"].'</td>';
			$mainline=$mainline. '<td>'.$row["Tutar"].'</td>';
		}
	$linequery = $db->query("with cte as (Select Recordid  , sum(Amount) as TotalAmount from RMS.dbo.Delivery 
	where Invoiceid ='$ara' and Company='$company' group by Recordid) 
	SELECT [sth_RECno] as Recordid,dbo.fn_DepoIsmi(sth_cikis_depo_no) as Depo,[sth_stok_kod] as Kod
	,dbo.fn_StokIsmi(sth_stok_kod) as isim,[sth_miktar] as Miktar,dbo.fn_StokBirimi(sth_stok_kod,sth_birim_pntr) as Birim
	,convert(nvarchar(30),cast(([sth_tutar]-([sth_iskonto1]+[sth_iskonto2]+[sth_iskonto3]+[sth_iskonto4]+[sth_iskonto5]+[sth_iskonto6])+[sth_vergi])/[sth_miktar] as money),1) as Fiyat
	,isnull(TotalAmount,0) as given , sth_miktar-isnull(TotalAmount,0) as remain 
	FROM [dbo].[STOK_HAREKETLERI] left outer join cte on cte.Recordid= sth_RECno 
    where sth_fat_recid_recno ='$ara'", PDO::FETCH_ASSOC);
	if ( $linequery->rowCount() )
	{
		
		$NotAllOfThemGiven=false;
		$NonOfThemGiven=true;
		$detail="";
		$i=0;
		foreach( $linequery as $linerow )
			{
				$i++;
				global $detail;
				if($linerow["remain"]>0){
				$detail.= '<tr class="detail ';
				if ($i % 2 == 1){
					$detail.='odd">';
				}else {
					$detail.='even">';
				}
				$detail.= '<td>'.$linerow["isim"].'</td>';
				$detail.= '<td>'.$linerow["Fiyat"];
				$detail.= '<div id="'.$linerow["Recordid"].'_Message" style="float:right;"><button id="'.$linerow["Recordid"].'" type="button" onclick="MakeDelivery(';
				$detail.= "'".$company."','".$ara."','".$linerow["Recordid"]."',".$linerow["remain"];
				$detail.= ')">Tam Teslim</button></div>';
				$detail.= '</td>';
				$detail.= '<td>Verilen : '.$linerow["given"].' '.$linerow["Birim"].'</td>';
				$detail.= '<td>Kalan : '.$linerow["remain"].' '.$linerow["Birim"].'</td>';
				$detail.= '</tr>';
				$NotAllOfThemGiven=true;
				}
				if($linerow["given"]>0)
				{
					$NonOfThemGiven=false;
				}

     		}
		if ($NotAllOfThemGiven)
		{
		global $mainline;
		if($NonOfThemGiven)
		{
			$mainline=$mainline.'<td>Hiçbir ürün teslim edilmedi.</td></tr>';
		}
		else
		{
			$mainline=$mainline.'<td>Bütün ürünler teslim edilmedi.</td></tr>';
		}
			$k++;
			$start= '<tr class="header expand ';
			if ($k % 2==1){
					$start.='odd" >';
				}else {
					$start.='even" >';
				}
			print $start;
			print $mainline;
			print $detail;
		}
	}
}
}
$db=null;
?>
</table>
</div>
</body>
</html>