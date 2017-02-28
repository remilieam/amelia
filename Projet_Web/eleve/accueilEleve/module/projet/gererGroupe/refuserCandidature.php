<!DOCTYPE html>

<?php
    require("../../../../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../../../../connexion.php");
    }
    
    // Récupération des identifiants et des noms du groupe, du projet et du module de la candidature
    if(isset($_GET["candid"]))
    {
        $ReqCan = "SELECT * FROM CANDIDATURE, APPARTIENT, GROUPE, PROJET, MODULE WHERE anneeModule LIKE '%".$_SESSION["_annee"]."%' AND idModule = idModulePere AND idProjet = idProjetGr AND idGroupe = idGroupeCa AND idGroupe = idGroupeAp AND loginEleveAp = '".$_COOKIE["_idf"]."' AND admin <> 0 AND idCandidature = ".$_GET["candid"];
        $TabCan = mysqli_query($BDD,$ReqCan);
        $LecCan = mysqli_fetch_array($TabCan);
        mysqli_free_result($TabCan);
        
        $idGroupe = $LecCan["idGroupe"];
        $nomGroupe = $LecCan["nomGroupe"];
        $idProjet = $LecCan["idProjet"];
        $nomProjet = $LecCan["nomProjet"];
        $idModule = $LecCan["idModule"];
        $nomModule = $LecCan["nomModule"];
        
        $message = $LecCan["texte"];
        $message = str_replace("'","’",$message);
        $message = str_replace(' "',' “',$message);
        $message = str_replace('"','”',$message);
    }
    
    // Vérification de l’URL
    if(!isset($_GET["candid"]) || $LecCan == NULL)
    {
?>
        <script>
            window.location.href = "../../../../accueilEleve.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../../../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../../../images/amelia.ico" />
        <title>AMÉLIA – Refuser une candidature</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../../../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../../../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr class="header">
                <td class="header"><a href="../../../../accueilEleve.php">Accueil</a> > <a href="../../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>"><?php echo $nomGroupe; ?></a> > Refuser une candidature</td>
                <td class="right"><a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Refuser une candidature</h2>
            
            <article>
                <form method="POST">
                    <p>Expliquer brièvement la raison de votre refus :</p>
                    <textarea name="_justif" rows=5></textarea>
                    <p>Voulez-vous vraiment refuser la candidature ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert" /> <input type="submit" name="_non" value="Non" class="orange" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Cas où l’élève veut vraiment refuser la candidature
    if(isset($_POST["_oui"]))
    {
        // Gestion des apostrophes
        $justif = $_POST["_justif"];
        $justif = str_replace("'","’",$justif);
        
        // Récupération du login de l’élève qu’on refuse d’intégrer
        $ReqLog = "SELECT * FROM CANDIDATURE WHERE idCandidature = ".$_GET["candid"];
        $TabLog = mysqli_query($BDD,$ReqLog);
        $LecLog = mysqli_fetch_array($TabLog);
        mysqli_free_result($TabLog);
        
        // Envoi du message de justification du refus
        $ReqJstf = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."','".$LecLog["loginEleveCa"]."','Refus de votre candidature dans le groupe ".$nomGroupe."','".$justif."')";
        
        // Modification de l’état de la candidature
        $ReqEtt = "UPDATE CANDIDATURE SET etat = 2 WHERE idCandidature = ".$_GET["candid"];
        
        if(mysqli_query($BDD,$ReqEtt) && mysqli_query($BDD,$ReqJstf))
        {
?>
            <script>
                alert("Votre requête a bien été exécutée !");
                window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre requête n’a pas pu être exécutée.\nVeuillez réessayer.");
                window.location.href = "accepterCandidature.php?candid=<?php echo $_GET["candid"]; ?>";
            </script>
<?php
        }
    }
    
    // Cas où l’élève se ravise
    elseif(isset($_POST["_non"]))
    {
?>
            <script>
                window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
    }
    
    mysqli_close($BDD);
?>