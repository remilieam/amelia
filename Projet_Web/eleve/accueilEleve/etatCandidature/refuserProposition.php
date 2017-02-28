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
        <title>AMÉLIA – Refuser une proposition</title>
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
                <td><a href="../../accueilEleve.php">Accueil</a> > <a href="../etatCandidature.php">État des candidatures</a> > Refuser une proposition</td>
                <td class="right"><a href="../etatCandidature.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Refuser une proposition</h2>
            
            <article>
                <form method="POST">
                    <p>Expliquer brièvement la raison de votre refus :</p>
                    <textarea name="_justif" rows=5></textarea>
                    <p>Voulez-vous vraiment refuser la proposition ?</p>
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
        // Gestion des apostrophes
        $justif = $_POST["_justif"];
        $justif = str_replace("'","’",$justif);
        
        // Récupération du login du propriétaire du groupe dans lequel on refuse d’aller
        $ReqLog = "SELECT * FROM CANDIDATURE, GROUPE, APPARTIENT WHERE admin = 2 AND idGroupeAp = idGroupe AND idGroupe =idGroupeCa AND idCandidature = ".$_GET["candid"];
        $TabLog = mysqli_query($BDD,$ReqLog);
        $LecLog = mysqli_fetch_array($TabLog);
        mysqli_free_result($TabLog);
        
        // Envoi du message de justification du refus
        $ReqJstf = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."','".$LecLog["loginEleveAp"]."','Refus de la proposition d’intégration dans votre groupe','".$justif."')";
        
        // Suppression de la candidature
        $ReqSupp = "DELETE FROM CANDIDATURE WHERE idCandidature = ".$_GET["candid"];
        
        if(mysqli_query($BDD,$ReqSupp) && mysqli_query($BDD,$ReqJstf))
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
                window.location.href = "refuserProposition.php?candid=".$_GET["candid"];
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