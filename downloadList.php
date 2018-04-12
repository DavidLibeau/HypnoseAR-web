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

$xml=FluidXml("<downloadlist></downloadlist>");

$files = scandir("./db/3d/xml");
foreach($files as $file) {
    //var_dump($file);
    $content=file_get_contents("./db/3d/xml/".$file, FILE_USE_INCLUDE_PATH);

    if($content===false){
        //die("Error:404");
    }else{
        $xml->query("/downloadlist")->addChild(FluidXml($content)->query("/object"));
    }
}

echo($xml->xml());

?>