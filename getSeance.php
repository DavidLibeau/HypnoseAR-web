<?php

if(isset($_GET["id"]) && $_GET["id"]!=""){
    $seanceId=$_GET["id"];
} elseif(isset($_COOKIE["seanceId"]) && $_COOKIE["seanceId"]!=""){
    $seanceId=$_COOKIE["seanceId"];
} else {
    die("Error:id?");
}

$content=file_get_contents("./db/seance-".$seanceId.".xml", FILE_USE_INCLUDE_PATH);

if($content===false){
    die("Error:404");
}else{
    echo($content);
}

?>