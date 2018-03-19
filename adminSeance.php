<?php

// Interface recherche
// Scene : Ajout d'objet en Ajax (bouton "Téléverser") avec vérification de l'XML local et XML distant (demande d'éraser si différence)


if(isset($_GET["id"]) && $_GET["id"]!=""){
    $seanceId=$_GET["id"];
} elseif(isset($_COOKIE["seanceId"]) && $_COOKIE["seanceId"]!=""){
    $seanceId=$_COOKIE["seanceId"];
} else {
    header("Location: index.php?error=Unknownseance");
}

$xml=simplexml_load_string(file_get_contents("./db/seance-".$seanceId.".xml", FILE_USE_INCLUDE_PATH));
$xml["isStarted"]="true";
$xml->saveXML("./db/seance-".$seanceId.".xml");

?>


    <!DOCTYPE html>

    <html lang="fr">

    <head>
        <meta charset="utf-8" />
        <title>Hypnose AR</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="theme-color" content="#0070C0">
        <link href="//dav.li/jquery/ui/jquery-ui.css" rel="stylesheet" />
        <style>
            #objects>span{
                display: inline-block;
                padding: 10px;
                margin: 10px;
                width: 300px;
                background-color: #fafafa;
                border-radius: 4px;
            }
            #objects>span>header>h3{
                margin: 10px 0;
            }
            #objects>span>div{
                margin: 10px 0;
                height: 200px;
                background-color: #f4f4f4;
            }
            #objects>span>footer>button{
                float: right;
            }
            
            #scene {
                height: 100vh;
                background-color: #f4f4f4;
            }
            #user {
                display: inline-block;
                position: absolute;
                width: 100px;
                height: 100px;
                background-color: grey;
                border-radius: 50%;
                top: 50%;
                left: 50%;
                margin: -50px;
            }
        </style>
    </head>

    <body>
        <h1>Hypnose AR</h1>
        <h2>Séance #
            <?php echo($seanceId); ?>
        </h2>

        <div id="tabs">
            <ul>
                <li><a href="#tab-objects">Objets</a></li>
                <li><a href="#tab-scene">Scene</a></li>
                <li><a href="#tab-param">Paramètres</a></li>
            </ul>
            <div id="tab-objects">
                <form>
                    <input type="text" placeholder="Recherche" disabled/>
                    <input type="submit" disabled/>
                </form>
                <div id="objects">
                    <?php
                    $objectsxml=scandir("./db/3d/xml/");
                    
                    if($objectsxml!=false){
                        foreach ($objectsxml as $objectxml){
                            if(strlen($objectxml)>=3){
                                $content=file_get_contents("./db/3d/xml/".$objectxml, FILE_USE_INCLUDE_PATH);
                                if($content!=false){
                                    $xml=simplexml_load_string($content);
                                    if($xml!=false){ ?>
                                    <span class="object">
                                        <header>
                                            <h3><?php echo($xml->name); ?></h3>
                                        </header>
                                        <div>

                                        </div>
                                        <footer>
                                            <button class="addObject" data-id="<?php echo($xml->id); ?>" data-name="<?php echo($xml->name); ?>">Ajouter</button>
                                        </footer>
                                    </span>
                                    <?php
                                    }
                                }
                            }
                        }
                    } ?>
                </div>
            </div>
            <div id="tab-scene">
                <div id="scene">
                    <span id="user"></span>
                </div>
            </div>
            <div id="tab-param">
                <p></p>
            </div>
        </div>


        <script type="text/javascript" src="//dav.li/jquery/2.1.4.js"></script>
        <script type="text/javascript" src="//dav.li/jquery/ui/jquery-ui.js"></script>
        <script>
            function getCookie(q){ //Retreive specific cookie
                var cookietable = document.cookie.split(";");
                for(var i=0; i<cookietable.length; i++){
                   if(cookietable[i].replace(" ","").split("=")[0]==q){
                        return cookietable[i].split("=")[1];
                   }
                }        
            }
            
            function updateDb(newObject){ //Update database
                console.log(getCookie("seanceId"));
                $.ajax({
                    url: "getSeance.php"
                }).done(function(data) {
                    console.log(data);
                    var xmlParser = new DOMParser();
                    var xmlDOM = xmlParser.parseFromString(data, "text/xml");
                    if(xmlDOM.documentElement.nodeName == "parsererror"){
                        
                    }else{
                        xmlDOM.getElementsByTagName("scene")[0].append(xmlParser.parseFromString(newObject, "text/xml").documentElement);
                        var xmlSerializer = new XMLSerializer();
                        console.log(xmlSerializer.serializeToString(xmlDOM));
                        $.ajax({
                            url: "updateSeance.php?data="+xmlSerializer.serializeToString(xmlDOM)
                        }).done(function(data) {
                            console.log(data);
                        });
                    }
                });
            }
            
            $(function() { //Jquery init tabs
                $("#tabs").tabs();
            });
            
            var addedObjectSceneId;
            var addedObjectSelfId;
            var addedObjectY;
            var addedObjectX;
            var holdObject=false;
            $(".addObject").click(function(){ //An object is selected
                $("#tabs").tabs("option", "active", 1);
                do {
                    addedObjectSceneId="o"+Math.floor(Math.random() * 9)+""+Math.floor(Math.random() * 9);
                } while ($("#scene .object[data-sceneId=\""+addedObjectSceneId+"\"]").length != 0);
                addedObjectSelfId=$(this).data("id");
                
                $("#scene").append("<span class=\"object\" data-selfId=\""+addedObjectSelfId+"\" data-sceneId=\""+addedObjectSceneId+"\">"+$(this).data("name")+" #"+$(this).data("id")+"</span>");
                holdObject=true;
            });
            
            $( document ).mousemove(function( event ) {
                if(holdObject){
                    $( "#scene .object[data-sceneId=\""+addedObjectSceneId+"\"]" ).position({
                        my: "center bottom",
                        of: event,
                        within: "#scene",
                        collision: "fit"
                    });
                    addedObjectY=-1*Math.round(($( "#scene .object[data-sceneId=\""+addedObjectSceneId+"\"]" ).offset().top+$( "#scene .object[data-sceneId=\""+addedObjectSceneId+"\"]" ).height()/2)-($( "#user" ).offset().top+$( "#user" ).height()/2))/100;
                    addedObjectX=Math.round(($( "#scene .object[data-sceneId=\""+addedObjectSceneId+"\"]" ).offset().left+$( "#scene .object[data-sceneId=\""+addedObjectSceneId+"\"]" ).width()/2)-($( "#user" ).offset().left+$( "#user" ).width()/2))/100;
                }
            });
            $("#scene").click(function(){
                if(holdObject){
                    holdObject=false;
                }
                updateDb("<object><selfId>"+addedObjectSelfId+"</selfId><sceneId>"+addedObjectSceneId+"</sceneId><position><x>"+addedObjectX+"</x><y>"+addedObjectY+"</y></position></object>");
            });
        </script>
    </body>

    </html>