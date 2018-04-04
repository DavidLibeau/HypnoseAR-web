<?php require("purgeDatabase.php"); ?>
<!DOCTYPE html>

<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>Hypnose AR</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="theme-color" content="#0070C0">
        <style>
            body{
                background:#f4f4f4;
                font-family: sans-serif;
            }
        </style>
    </head>
    <body>
        <h1>Hypnose AR</h1>
        
        <?php if(isset($_GET["error"])){ ?>
        <p style="color:red">Error : <strong><?php echo($_GET["error"]); ?></strong></p>
        <?php } ?>
        
        <form action="joinSeance.php" method="get">
            <label>
            Rejoindre une séance :
                <input type="text" name="id" placeholder="id"/>
            </label>
            <input type="submit"/>
        </form>

        <script type="text/javascript" src="//dav.li/jquery/2.1.4.js"></script>
    </body>
</html>
