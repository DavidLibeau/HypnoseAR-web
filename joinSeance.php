<?php

function time_elapsed($secs){
    $bit = array(
        ' année'        => $secs / 31556926 % 12,
        ' semaine'        => $secs / 604800 % 52,
        ' jour'        => $secs / 86400 % 7,
        ' heure'        => $secs / 3600 % 24,
        ' minute'    => $secs / 60 % 60,
        ' seconde'    => $secs % 60
        );

    foreach($bit as $k => $v){
        if($v > 1)$ret[] = $v . $k . 's';
        if($v == 1)$ret[] = $v . $k;
        }
    array_splice($ret, count($ret)-1, 0, 'et');
    $ret[] = '';

    return join(' ', $ret);
}

function isSeanceStarted($id) {
    $filecontent=file_get_contents("./db/seance-".$id.".xml", FILE_USE_INCLUDE_PATH);
    if($filecontent===false){
        return false;
    }else{
        return true;   
    }
}

if(!(isset($_GET["id"]) && $_GET["id"]!="" && isSeanceStarted($_GET["id"]))){
    header("Location: index.php?error=Unknownseance");
}else{
    setcookie("seanceId",$_GET["id"],time()+10*6000);
    $seanceId=$_GET["id"];
    
    $xml=simplexml_load_string(file_get_contents("./db/seance-".$seanceId.".xml", FILE_USE_INCLUDE_PATH));
    $date_create=$xml->date_create;
    
?>

    <!DOCTYPE html>

    <html lang="fr">

    <head>
        <meta charset="utf-8" />
        <title>Hypnose AR</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="theme-color" content="#0070C0">
    </head>

    <body>
        <h1>Hypnose AR</h1>
        <h2>Vous avez rejoint #<?php echo($seanceId); ?></h2>
        <p>Séance débutée il y a <?php echo(time_elapsed(time()-intval($date_create))); ?>!</p>
        
        <p><a href="adminSeance.php"><button>Administrer cette séance</button></a></p>

        <script type="text/javascript" src="//dav.li/jquery/2.1.4.js"></script>
    </body>

    </html>

<?php 
} 
?>