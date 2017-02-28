<!DOCTYPE html>

<?php
    require("../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../connexion.php");
    }
    
    // Récupération du message
    if(isset($_GET["message"]))
    {
        $ReqMsg = "SELECT * FROM MESSAGE WHERE idMessage = ".$_GET["message"]." AND (loginRecoi = '".$_COOKIE["_idf"]."' OR loginEnvoi = '".$_COOKIE["_idf"]."')";
        $TabMsg = mysqli_query($BDD,$ReqMsg);
        $LecMsg = mysqli_fetch_array($TabMsg);
        mysqli_free_result($TabMsg);
        
        // Mise à jour du message : il est désormais lu (lu <- 1)
        $ReqLct = "UPDATE MESSAGE SET lu = 1 WHERE loginRecoi = '".$_COOKIE["_idf"]."' AND idMessage = ".$_GET["message"];
        mysqli_query($BDD,$ReqLct);
        
        $sujet = $LecMsg["sujet"];
        $sujet = str_replace("'","’",$sujet);
        $sujet = str_replace(' "',' “',$sujet);
        $sujet = str_replace('"','”',$sujet);
        
        $message = $LecMsg["message"];
        $message = str_replace(' "',' “',$message);
        $message = str_replace('"','”',$message);
    }
    
    // Vérification de l’URL
    if(!isset($_GET["message"]) || $LecMsg == NULL)
    {
?>
        <script>
            window.location.href = "../compte.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../stylesheet.css" />
        <link rel="shortcut icon" href="../images/amelia.ico" />
        <title>AMÉLIA – Lire un message</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr>
                <td><a href="../compte.php">Mon compte</a> > Lire un message</td>
                <td class="right"><a href="../compte.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
        
            <h2><?php echo $sujet; ?></h2>
            
            <article>
                <p><?php echo $message; ?></p>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    mysqli_close($BDD);
?>