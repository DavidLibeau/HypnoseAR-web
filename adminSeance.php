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
                <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
                <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
            </div>
        </div>


        <script type="text/javascript" src="//dav.li/jquery/2.1.4.js"></script>
        <script type="text/javascript" src="//dav.li/jquery/ui/jquery-ui.js"></script>
        <script>
            $(function() {
                $("#tabs").tabs();
            });
            
            var addedObjectId;
            $(".addObject").click(function(){
                $("#tabs").tabs("option", "active", 1);
                $("#scene").append("<span class=\"object\" data-id=\""+$(this).data("id")+"\">"+$(this).data("name")+" #"+$(this).data("id")+"</span>");
                addedObjectId=$(this).data("id");
            });
            
            $( document ).mousemove(function( event ) {
              $( "#scene .object[data-id=\""+addedObjectId+"\"]" ).position({
                my: "left+3 bottom-3",
                of: event,
                collision: "fit"
              });
            });
        </script>
    </body>

    </html>