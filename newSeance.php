<?php

function isSeanceExist($id) {
    $filecontent=file_get_contents("./db/seance-".$id.".xml", FILE_USE_INCLUDE_PATH);
    if($filecontent===false){
        return false;
    }else{
        return true;   
    }
}

function randomChar() {
    if(rand(0,1)==0){ //letter
        return substr("ABCDEFGHIJKLMNOPQRSTUVWXYZ", rand(0,25), 1);
    }else{ //number
        return rand(0,9);
    }
}

function randomId() {
    return randomChar().randomChar().randomChar().randomChar().randomChar();
}

do {
   $id=randomId(); 
} while(isSeanceExist($id));

$xml=simplexml_load_string("<seance isSarted=\"false\"><id>".$id."</id><date_create>".time()."</date_create><scene></scene></seance>");

$xml->saveXML("./db/seance-".$id.".xml");

echo($id);

?>