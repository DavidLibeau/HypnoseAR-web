<?php
$files = glob("db/seance-*.xml");
foreach($files as $file) {
    $xml=simplexml_load_file($file);
    echo("\n\r");
    if((time()-(60*60*24*7)) > $xml->date_create){ //tous les 7 jours
        unlink($file);
    }
}
?>