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
        <title>AMÉLIA – Accepter une proposition</title>
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
                <td><a href="../../accueilEleve.php">Accueil</a> > <a href="../etatCandidature.php">État des candidatures</a> > Accepter une proposition</td>
                <td class="right"><a href="../etatCandidature.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Accepter une proposition</h2>
            
            <article>
                <form method="POST">
                    <p>Voulez-vous vraiment accepter la proposition ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert" /> <input type="submit" name="_non" value="Non" class="orange" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Cas où l’élève veut vraiment supprimer la candidature
    if(isset($_POST["_oui"]))
    {
        // Récupération de l’identifiant du groupe
        $ReqCan = "SELECT * FROM CANDIDATURE WHERE idCandidature = ".$_GET["candid"];
        $TabCan = mysqli_query($BDD,$ReqCan);
        $LecCan = mysqli_fetch_array($TabCan);
        mysqli_free_result($TabCan);
        
        // Suppression de la candidature et ajout de l’élève au groupe dans lequel il a candidaté
        $ReqAccp = "INSERT INTO APPARTIENT (loginEleveAp, idGroupeAp) VALUES ('".$_COOKIE["_idf"]."', ".$LecCan["idGroupeCa"].")";
        $ReqSupp = "DELETE FROM CANDIDATURE WHERE idCandidature = ".$_GET["candid"];
        
        if(mysqli_query($BDD,$ReqAccp) && mysqli_query($BDD,$ReqSupp))
        {
?>
            <script>
                alert("Votre suppression a bien été enregistrée !");
                window.location.href = "../etatCandidature.php";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre suppression n’a pas pu être enregistrée.\nVeuillez réessayer.");
                window.location.href = "accepterProposition.php?candid=".$_GET["candid"];
            </script>
<?php
        }
    }
    
    // Cas où l’élève se ravise
    elseif(isset($_POST["_non"]))
    {
        header("Location: ../etatCandidature.php");
        exit();
    }
    
    mysqli_close($BDD);
?>