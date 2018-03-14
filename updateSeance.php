<?php

if(isset($_GET["id"]) && $_GET["id"]!=""){
    $seanceId=$_GET["id"];
} elseif(isset($_COOKIE["seanceId"]) && $_COOKIE["seanceId"]!=""){
    $seanceId=$_COOKIE["seanceId"];
} else {
    die("Error:id?");
}

if(isset($_GET["data"]) && $_GET["data"]!=""){
    $data=$_GET["data"];
} else {
    die("Error:data?");
}

$return=file_put_contents("./db/seance-".$seanceId.".xml", $data, FILE_USE_INCLUDE_PATH);

if($return===false){
    die("Error:404");
}else{
    echo($return."o successfully writed!");
}

?>