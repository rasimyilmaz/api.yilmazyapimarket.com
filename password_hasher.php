<?php
$password="1905-kal-em";
$passwordHash=password_hash($password,PASSWORD_DEFAULT);
if(password_verify($password,$passwordHash)){
	echo "password correct!<br/>";
	echo $passwordHash;
}else{
	echo "password wrong!";
}
?>