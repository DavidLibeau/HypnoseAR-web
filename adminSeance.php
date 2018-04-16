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
        <link rel="stylesheet" href="https://dav.li/forkawesome/1.0.11/css/fork-awesome.min.css" />
        <style>
            body {
                background: #f4f4f4;
                font-family: sans-serif;
                overflow-x: hidden;
            }

            #objects>span {
                display: inline-block;
                padding: 10px;
                margin: 10px;
                background-color: #fafafa;
                border-radius: 4px;
            }

            #objects>span>header>h3 {
                margin: 10px 0;
            }

            #objects>span>div {
                margin: 10px 0;
                height: 200px;
                background-color: #f4f4f4;
                text-align: center;
                overflow: hidden;
            }
            #objects>span>div>img {
                height: 200px;
            }

            #objects>span>footer>button {
                float: right;
            }

            #scene {
                position: relative;
                height: 100vh;
                background-color: #f4f4f4;
            }

            #tab-scene>.refreshScene {
                position: absolute;
                top: 24px;
                right: 10px;
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

            #scene>.object {
                position: absolute;
                cursor: pointer;
            }

            #scene>.object>header {
                z-index: 1;
            }

            #scene>.object>footer {
                display: none;
                cursor: default;
            }

            #scene>.object.open>footer {
                display: block;
                position: absolute;
                min-width: 350px;
                z-index: 9;
            }

            #scene>.object .ui-tabs-nav a {
                font-size: 15px;
                padding: 5px;
            }

            #scene>.object .ui-tabs-panel {
                padding: 5px;
            }

            #scene>.object .ui-tabs-panel p {
                margin: 5px;
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
                    $content=file_get_contents("./db/3d/downloadList.xml", FILE_USE_INCLUDE_PATH);
                    if($content!=false){
                        $xml=simplexml_load_string($content);
                        $xml=FluidXml($xml);
                        $xml->query("//object")->each(function($i, $DOMnode) {
                            ?>
                        <span class="object">
                            <header>
                                <h3><?php echo($this->query("name/text()")); ?></h3>
                            </header>
                            <div>
                                <img src="db/3d/thumbnail/<?php echo($this->query("id/text()")); ?>_thumbnail.png" alt="Thumbnail"/>
                            </div>
                            <footer>
                                <button class="addObject" data-id="<?php echo($this->query("id/text()")); ?>" data-name="<?php echo($this->query("name/text()")); ?>">Ajouter</button>
                            </footer>
                        </span>
                        <?php
                        });
                    } ?>
                </div>
            </div>
            <div id="tab-scene">
                <button class="refreshScene">Rafraîchir la Scene</button>
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
            function getCookie(q) { //Retreive specific cookie
                var cookietable = document.cookie.split(";");
                for (var i = 0; i < cookietable.length; i++) {
                    if (cookietable[i].replace(" ", "").split("=")[0] == q) {
                        return cookietable[i].split("=")[1];
                    }
                }
            }

            function refreshScene() {
                $.ajax({
                    url: "getSeance.php?id=" + getCookie("seanceId")
                }).done(function(data) {
                    console.log(data);
                    var xmlParser = new DOMParser();
                    var xmlDOM = xmlParser.parseFromString(data, "text/xml");
                    
                    if (xmlDOM.documentElement.nodeName == "parsererror") {

                    } else {
                        console.log(xmlDOM.getElementsByTagName("scene")[0].childNodes);
                        xmlDOM.getElementsByTagName("scene")[0].childNodes.forEach(function(obj) {
                            if (obj.nodeType == Node.ELEMENT_NODE) {
                                console.log(obj);
                                var sceneId = obj.getElementsByTagName("sceneId")[0].textContent;
                                var selfId = obj.getElementsByTagName("selfId")[0].textContent;

                                var posY = $("#scene").height() / 2 - (parseFloat(obj.querySelector("position>y").textContent) * 100);
                                //console.log(parseFloat(obj.querySelector("position>x").textContent) * 100);
                                var posX = $("#scene").width() / 2 + (parseFloat(obj.querySelector("position>x").textContent) * 100);

                                $("#scene>span[data-sceneId=\"" + sceneId + "\"]").remove();
                                $("#scene").append("<span class=\"object\" data-selfId=\"" + selfId + "\" data-sceneId=\"" + sceneId + "\" style=\"top: " + posY + "px; left: " + posX + "px;\">" +
                                    "<header><i class=\"fa fa-caret-right\" aria-hidden=\"true\"></i> "+getNameOfObject(selfId)+" #" + sceneId + "</header>" +
                                    "<footer " + (parseFloat(obj.querySelector("position>x").textContent) < 0 ? "" : "style=\"right:0\"") + "><div id=\"objectTab-" + sceneId + "\"><ul><li><a href=\"#objectTab-" + sceneId + "-position\">Position</a></li><li><a href=\"#objectTab-" + sceneId + "-rotation\">Rotation</a></li><li><a href=\"#objectTab-" + sceneId + "-scale\">Scale</a></li></ul>" +
                                    "<div id=\"objectTab-" + sceneId + "-position\"><p><label>X <input type=\"number\" name=\"posX\" value=\"" + obj.querySelector("position>x").textContent + "\"/></label></p><p><label>Y <input type=\"number\" name=\"posY\" value=\"" + obj.querySelector("position>y").textContent + "\"/></label></p><p><label>Z <input type=\"number\" name=\"posZ\" value=\"" + obj.querySelector("position>z").textContent + "\"/></label></p></form></div>" +
                                    "<div id=\"objectTab-" + sceneId + "-rotation\"><p><label>X <input type=\"number\" name=\"posX\" value=\"" + obj.querySelector("position>x").textContent + "\"/></label></p><p><label>Y <input type=\"number\" name=\"posY\" value=\"" + obj.querySelector("position>y").textContent + "\"/></label></p><p><label>Z <input type=\"number\" name=\"posZ\" value=\"" + obj.querySelector("position>z").textContent + "\"/></label></p></form></div>" +
                                    "<div id=\"objectTab-" + sceneId + "-scale\"><p><label>X <input type=\"number\" name=\"posX\" value=\"" + obj.querySelector("position>x").textContent + "\"/></label></p><p><label>Y <input type=\"number\" name=\"posY\" value=\"" + obj.querySelector("position>y").textContent + "\"/></label></p><p><label>Z <input type=\"number\" name=\"posZ\" value=\"" + obj.querySelector("position>z").textContent + "\"/></label></p></div>" +
                                    "<button class=\"updateObject\">Mettre à jour l'objet</button></footer</span>");

                                $("#objectTab-" + sceneId).tabs();
                                $("#scene>.object[data-sceneId=\"" + sceneId + "\"] button.updateObject").click(function() {
                                    var posX = $("#scene>.object[data-sceneId=\"" + sceneId + "\"] [name=\"posX\"]").val();
                                    var posY = $("#scene>.object[data-sceneId=\"" + sceneId + "\"] [name=\"posY\"]").val();
                                    var posZ = $("#scene>.object[data-sceneId=\"" + sceneId + "\"] [name=\"posZ\"]").val();
                                    var objectXml = "<object><selfId>" + selfId + "</selfId><sceneId>" + sceneId + "</sceneId><position><x>" + posX + "</x><y>" + posY + "</y><z>" + posZ + "</z></position></object>";
                                    console.log(objectXml);
                                    $.ajax({
                                        url: "updateObject.php?seanceId=" + getCookie("seanceId") + "&data=" + objectXml
                                    }).done(function(data) {
                                        console.log(data);
                                        $("#scene>.object[data-sceneId=\"" + sceneId + "\"] button.updateObject").text(data);
                                        setTimeout(function() {
                                            $("#scene>.object[data-sceneId=\"" + sceneId + "\"] button.updateObject").text("Update object");
                                        }, 5000);
                                    }).fail(function(data) {
                                        console.log(data);
                                    });
                                });

                            }
                        });
                        $("#scene>.object>header").click(function() {
                            if ($(this).parent().hasClass("open")) {
                                $(this).parent().removeClass("open");
                                $(this).children(".fa-caret-down").removeClass("fa-caret-down").addClass("fa-caret-right");
                            } else {
                                $(this).parent().addClass("open");
                                $(this).children(".fa-caret-right").removeClass("fa-caret-right").addClass("fa-caret-down");
                            }
                        });
                    }
                });
            }
            
            var downloadList;
            function getNameOfObject(id){
                parser = new DOMParser();
                var downloadListXml = parser.parseFromString(downloadList,"text/xml");
                console.log(downloadListXml);
                var it= downloadListXml.evaluate("//object[id="+id+"]/name/text()", downloadListXml, null, XPathResult.ANY_TYPE, null );
                var names = [];
                var node;
                while (node = it.iterateNext()) {names.push(node);}
                return names[0].textContent;
            }

            $(function() {
                $("#tabs").tabs();
                $("#tabs").tabs("option", "active", 1);
                
                $.ajax({
                    url: "downloadList.php"
                }).done(function(data) {
                    downloadList=data;
                });

                setTimeout(function() {
                    refreshScene();
                    //setInterval(refreshScene, 5000);
                }, 500);

                $(".refreshScene").click(function() {
                    refreshScene();
                    $(".refreshScene").text("Scene rafraîchie !");
                    setTimeout(function() {
                        $(".refreshScene").text("Rafraîchir la Scene");
                    }, 1500);
                });
            });

            var addedObjectSceneId;
            var addedObjectSelfId;
            var addedObjectY;
            var addedObjectX;
            var holdObject = false;
            $(".addObject").click(function() { //An object is selected
                $("#tabs").tabs("option", "active", 1);
                do {
                    addedObjectSceneId = Math.floor(Math.random() * 9) + "" + Math.floor(Math.random() * 9);
                } while ($("#scene .object[data-sceneId=\"" + addedObjectSceneId + "\"]").length != 0);
                addedObjectSelfId = $(this).data("id");

                $("#scene").append("<span class=\"object\" data-selfId=\"" + addedObjectSelfId + "\" data-sceneId=\"" + addedObjectSceneId + "\">" + $(this).data("name") + " #" + $(this).data("id") + "</span>");
                holdObject = true;
            });

            $(document).mousemove(function(event) {
                if (holdObject) {
                    $("#scene .object[data-sceneId=\"" + addedObjectSceneId + "\"]").position({
                        my: "top left",
                        of: event,
                        within: "#scene",
                        collision: "fit"
                    });
                    addedObjectY = -1 * Math.round(($("#scene .object[data-sceneId=\"" + addedObjectSceneId + "\"]").offset().top + $("#scene .object[data-sceneId=\"" + addedObjectSceneId + "\"]").height() / 2) - ($("#user").offset().top + $("#user").height() / 2)) / 100;
                    addedObjectX = Math.round(($("#scene .object[data-sceneId=\"" + addedObjectSceneId + "\"]").offset().left + $("#scene .object[data-sceneId=\"" + addedObjectSceneId + "\"]").width() / 2) - ($("#user").offset().left + $("#user").width() / 2)) / 100;
                }
            });
            $("#scene").click(function() {
                if (holdObject) {
                    holdObject = false;
                    $.ajax({
                        url: "addObject.php?seanceId=" + getCookie("seanceId") + "&data=<object><selfId>" + addedObjectSelfId + "</selfId><sceneId>" + addedObjectSceneId + "</sceneId><position><x>" + addedObjectX + "</x><y>" + addedObjectY + "</y><z>1</z></position></object>"
                    }).done(function(data) {
                        refreshScene();
                        console.log(data);
                    });
                }
            });
        </script>
    </body>

    </html>