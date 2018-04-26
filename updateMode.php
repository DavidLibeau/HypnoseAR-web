<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("lib/fluidxml-1.21/source/FluidXml.php");
use \FluidXml\FluidXml;
use \FluidXml\FluidNamespace;
use function \FluidXml\fluidxml;
use function \FluidXml\fluidns;
use function \FluidXml\fluidify;

if(isset($_GET["seanceId"]) && $_GET["seanceId"]!=""){
    $seanceId=$_GET["seanceId"];
} else {
    die("Error:seanceId?");
}


if ($_SERVER['REQUEST_METHOD'] === "PUT") {
    $putdata = fopen("php://input", "r");
    $putcontent=fread($putdata, filesize($putdata));
    fclose($putdata);
}elseif(isset($_GET["data"]) && $_GET["data"]!=""){
    $putcontent=$_GET["data"];
}else{
    die("Error:PUT data");
}

$putcontent=FluidXml(simplexml_load_string($putcontent));

$xml=FluidXml(file_get_contents("./db/seance-".$seanceId.".xml", FILE_USE_INCLUDE_PATH));
$simplexml=simplexml_load_string(file_get_contents("./db/seance-".$seanceId.".xml", FILE_USE_INCLUDE_PATH));

$modified=false;
$putcontent->query("//mode")->each(function($i, $domnode) {
    global $xml,$modified,$putcontent,$simplexml;
    $object=FluidXml($domnode);
    
    $modified=true;
    $xml->remove("//mode");
    $xml->query("scene")->addChild($object);
});


if($modified){
    $return=file_put_contents("./db/seance-".$seanceId.".xml", $xml, FILE_USE_INCLUDE_PATH);

    if($return===false){
        die("Error:404");
    }else{
        echo($return."o successfully writed!");
    }
}else{
    echo("No object added.");
}



// curl -X PUT -H "Content-Type: application/json" -d '<objects><object><selfId>1</selfId><sceneId>o24</sceneId><position><x>3</x><y>-5</y><z>2</z></position></object></objects>' "http://dav.li/HypnoseAR/addObject.php?seanceId=1578T"

//<objects><object><selfId>1</selfId><sceneId>o24</sceneId><position><x>3</x><y>-5</y><z>2</z></position></object></objects>

//<objects><object><position><x>3</x></position></object></objects>
?>