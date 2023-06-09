<?php
function send_response($value){
    echo json_encode($value);
}
function add_term($base,$value){
	if(strlen($base)>0){
		$reply=$base+' AND '+$value;
	}else {
		$reply=$value;
	}
	return $reply; 
}
?>