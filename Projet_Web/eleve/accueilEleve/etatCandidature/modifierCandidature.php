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
    
    // Récupération de la candidature
    if(isset($_GET["candid"]))
    {
        $ReqMsg = "SELECT * FROM CANDIDATURE WHERE idCandidature = ".$_GET["candid"];
        $TabMsg = mysqli_query($BDD,$ReqMsg);
        $LecMsg = mysqli_fetch_array($TabMsg);
        mysqli_free_result($TabMsg);
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
        <title>AMÉLIA – Modifier une candidature</title>
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
            <td>
                <a href="../../accueilEleve.php">Accueil</a> > <a href="../etatCandidature.php">État des candidatures</a> > Modifier une candidature</td>
                <td class="right"><a href="../etatCandidature.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
                
            <h2>Modifier une candidature</h2>
            
            <article>
                <form method="POST">
                    <p>Écrivez ici votre nouveau message (raison de votre candidature, motivation, compétences, etc.)</p>
                    <textarea name="_message" rows=5></textarea>
                    <p class="right"><input type="submit" name="_envoyer" value="Envoyer" class="vert" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_envoyer"]))
    {
        // Gestion des apostrophes
        $message = $_POST["_message"];
        $message = str_replace("'","’",$message);
        
        // Modification de la candidature
        $RqtMaj = "UPDATE CANDIDATURE SET texte = '".$message."' WHERE idCandidature = ".$_GET["candid"];
        
        if(mysqli_query($BDD,$RqtMaj))
        {
?>
            <script>
                alert("Votre modification a bien été enregistrée !");
                window.location.href = "../etatCandidature.php";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre modification n’a pas pu être enregistrée.\nVeuillez réessayer.");
                window.location.href = "modifierCandidature.php?candid=".$_GET["candid"];
            </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>