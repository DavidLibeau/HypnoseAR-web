<?php
function appendXML($parent,$child){
    if (strlen(trim((string)$child)) == 0) {
        $childAdded=$parent->addChild($child->getName());
    } else {
        $childAdded=$parent->addChild($child->getName(), (string)$child);
    }
    //echo($parent->asXML());
    //echo("\r\n".$childAdded->getName()." : ");
    //var_dump($childAdded);

    $childName=$child->getName();
    foreach ($child->children() as $subchild) {
        //echo("\r\n------\r\n");
        appendXML($childAdded,$subchild);  
    }
    foreach ($child->attributes() as $n => $v) {
        $parent->addAttribute($n, $v);
    }
}

////

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

$putcontent=simplexml_load_string($putcontent);

$xml=simplexml_load_string(file_get_contents("./db/seance-".$seanceId.".xml", FILE_USE_INCLUDE_PATH));

$modified=false;
foreach($putcontent->object as $object){
    if(count($xml->xpath("//object[sceneId=".$object->sceneId."]"))<1){
        $modified=true;
        appendXML($xml->scene,$object);
    }
}
echo($xml->asXML());

if($modified){
    $return=file_put_contents("./db/seance-".$seanceId.".xml", $xml->asXML(), FILE_USE_INCLUDE_PATH);

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