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
        <title>AMÉLIA – Supprimer un message</title>
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
                <td><a href="../compte.php">Mon compte</a> > Supprimer un message</td>
                <td class="right"><a href="../compte.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Supprimer un message</h2>
            
            <article>
                <form method="POST">
                    <table class="large">
                        <tr>
                            <td>Voulez-vous vraiment supprimer le message ?</td>
                            <td class="right"><input type="submit" name="_oui" value="Oui" class="vert" /> <input type="submit" name="_non" value="Non" class="orange" /></td>
                        </tr>
                    </table>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Détermination de la personne qui supprime le message
    $ReqMsg = "SELECT * FROM MESSAGE WHERE loginRecoi = '".$_COOKIE["_idf"]."' AND idMessage = ".$_GET["message"];
    $TabMsg = mysqli_query($BDD,$ReqMsg);
    $LecMsg = mysqli_fetch_array($TabMsg);
    mysqli_free_result($TabMsg);
    
    // Cas où l’utilisateur veut réellement supprimer le message
    if(isset($_POST["_oui"]))
    {
        // Cas où c’est l’envoyeur qui supprime le message
        if($LecMsg == NULL)
        {
            $ReqSupr = "UPDATE MESSAGE SET supprEnvoi = 1 WHERE idMessage = ".$_GET["message"];
            
            if(mysqli_query($BDD,$ReqSupr))
            {
?>
                <script>
                    alert("Votre requête a bien été enregistrée !");
                    window.location.href = "../compte.php";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre requête n’a pas pu être enregistrée.\nVeuillez réessayer.");
                    window.location.href = "../compte.php";
                </script>
<?php
            }
        }
        
        // Cas où c’est le receveur qui supprime le message
        else 
        {
            $ReqSupr = "UPDATE MESSAGE SET supprRecoi = 1 WHERE idMessage = ".$_GET["message"];
            
            if(mysqli_query($BDD,$ReqSupr))
            {
?>
                <script>
                    alert("Votre requête a bien été enregistrée !");
                    window.location.href = "../compte.php";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre requête n’a pas pu être enregistrée.\nVeuillez réessayer.");
                    window.location.href = "../compte.php";
                </script>
<?php
            }
        }
        
        // Suppression définitive dans le cas où l’envoyeur et le receveur ont supprimé le message
        $ReqDef = "DELETE FROM MESSAGE WHERE supprEnvoi <> 0 AND supprRecoi <> 0 AND idMessage = ".$_GET["message"];
        mysqli_query($BDD,$ReqDef);
    }
    
    // Cas où l’utilisateur ne veut plus supprimer le message
    elseif(isset($_POST["_non"]))
    {
        header("Location: ../compte.php");
        exit();
    }
    
    mysqli_close($BDD);
?>