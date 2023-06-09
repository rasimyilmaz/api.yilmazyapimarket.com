<?php
header('Content-Type: application/json');
echo json_encode($_SERVER);
$success=false;
$message = "";
$target_dir = getcwd();
$mime = "text/xml";
$fileext= "xml";
$maxfilesize = 10;
$apikey = "vlWnynLs82X6h9J";
$username = "rasim";
$passwordHash = '$2y$10$Gyh4UjvNYQ8KzsHBcQZy3.FLdjxacjQK7S5GPOQ/lZxF6zTULMwci';
$X_API_KEY = $_SERVER["HTTP_X_API_KEY"];
if(strcmp($apikey,$X_API_KEY)==0){
    if(isset($_POST["username"]) and isset($_POST["password"]) and count($_FILES)>0){
        if(strcmp($_POST["username"],$username)==0 and password_verify($_POST["password"],$passwordHash)){
        $ext=strtolower(pathinfo($_FILES["file_products_xml"]["name"],PATHINFO_EXTENSION));
            $target_file = "products.xml";
            if (isset($_FILES["file_products_xml"])){
                if(strcmp($ext,"xml") == 0) {//mime_content_type($_FILES["file_pruducts_xml"]["tmp_name"])
                    if(strcmp(strval(mime_content_type($_FILES["file_pruducts_xml"]["tmp_name"])),$mime)==0){
                        if ($_FILES["file_products_xml"]["size"] <  $maxfilesize * 1024 * 1024) {
                            move_uploaded_file($_FILES["file_products_xml"]["tmp_name"], $target_file);
                            $message="Başarıyla yüklendi.";
                            $success=true;
                        } else {
                            $message = "Dosya 10mb dan büyük";
                        }
                    } else {
                        $message="Dosya türü ".$mime." değil, bkz:".strval(strcmp(mime_content_type($_FILES["file_pruducts_xml"]["tmp_name"]),$mime))."->".mime_content_type($_FILES["file_products_xml"]["tmp_name"]).":".$mime;
                    }
                } else {
                $message= $fileext." dosyası değil";
                }
            } else {
                $message= "Dosya yok";
            }
        } else {
            $message="Kullanıcı adı veya parola hatalı".$_POST["username"].":".$passwordHash;
        }} else {
        $message = "Parametre eksik";
    }} else {
$message="Api anahtarı yanlış!";
}
$data = ["success"=>$success,"message"=>$message];
echo json_encode($data);
?>