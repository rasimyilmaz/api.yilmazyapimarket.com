<?php
function build_query(stdClass $data,bool $flag_product_code,bool $flag_product_barcode,bool $flag_product_price){
	$header="SELECT * FROM rasimyilmaz_products";
	$product_code=trim($data->{'product_code'});
	$keywords=trim($data->{'product_name'});
	$product_barcode=strval($data->{'product_barcode'});
	$criteria="";
	$slices = explode(" ", $keywords);
	if($flag_product_code){
		$criteria=add_term($criteria,"code LIKE '%$product_code%'");
	}
	for ($i=0;$i<count($slices);$i++){
		$criteria=add_term($criteria,"name LIKE '%$slices[$i]%'");
	}
	if($flag_product_barcode){
		$criteria=add_term($criteria,"barcode LIKE '%$product_barcode%'");
	}
	if($flag_product_price){
		$criteria=add_term($criteria,"price = :price");
	}
	return $header .' WHERE '.$criteria;;
}
function get_debtaccountinfo_query($day){
	$query="SELECT code,fullname,account_type,city,mobile,balance,day FROM rasimyilmaz_debtaccountinfo WHERE day>='strval($day)' ORDER BY balance";
	return $query;
}
?>