<!DOCTYPE html>

<?php
    require("../../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../../connexion.php");
    }
    
    // Récupération du texte de la candidature
    if(isset($_GET["candid"]))
    {
        $ReqMsg = "SELECT * FROM CANDIDATURE WHERE idCandidature = ".$_GET["candid"];
        $TabMsg = mysqli_query($BDD,$ReqMsg);
        $LecMsg = mysqli_fetch_array($TabMsg);
        mysqli_free_result($TabMsg);
        
        $Msg = $LecMsg["texte"];
        $Msg = str_replace(' "',' “',$Msg);
        $Msg = str_replace('"','”',$Msg);
    }
    
    // Vérification de l’URL
    if(!isset($_GET["candid"]) || $LecMsg == NULL)
    {
?>
        <script>
            window.location.href = "../../accueilEleve.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../images/amelia.ico" />
        <title>AMÉLIA – Voir une candidature</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr>
                <td><a href="../../accueilEleve.php">Accueil</a> > <a href="../etatCandidature.php">État des candidatures</a> > Voir une candidature</td>
                <td class="right"><a href="../etatCandidature.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Voir une candidature</h2>
            
            <article>
                <p><?php echo $Msg; ?></p>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    mysqli_close($BDD);
?>