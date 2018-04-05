<?php
require("lib/fluidxml-1.21/source/FluidXml.php");
use \FluidXml\FluidXml;
use \FluidXml\FluidNamespace;
use function \FluidXml\fluidxml;
use function \FluidXml\fluidns;
use function \FluidXml\fluidify;

if(isset($_GET["seanceId"]) && $_GET["seanceId"]!=""){
    $seanceId=$_GET["seanceId"];
} elseif(isset($_COOKIE["seanceId"]) && $_COOKIE["seanceId"]!=""){
    $seanceId=$_COOKIE["seanceId"];
} else {
    header("Location: index.php?error=Unknownseance");
}

if(isset($_GET["objectId"]) && $_GET["objectId"]!=""){
    $objectId=$_GET["objectId"];
}else {
    die("objectId?");
}

$content=file_get_contents("./db/seance-".$seanceId.".xml", FILE_USE_INCLUDE_PATH);

if($content===false){
    die("Error:404(seance)");
}else{
    $xml=FluidXml($content);
    $xml->remove("//object[sceneId=".$objectId."]");
    $xml->save("./db/seance-".$seanceId.".xml");
    echo("Done!");
}

?>