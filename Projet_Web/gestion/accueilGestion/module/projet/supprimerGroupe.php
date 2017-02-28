<!DOCTYPE html>

<?php
    require("../../../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../../../connexion.php");
    }
    
    // Récupération des identifiants et des noms du groupe, du projet et du module auquel appartient le projet
    if(isset($_GET["groupe"]))
    {
        $ReqGrp = "SELECT * FROM GROUPE, PROJET, MODULE WHERE idModule = idModulePere AND idProjet = idProjetGr AND idGroupe = ".$_GET["groupe"];
        $TabGrp = mysqli_query($BDD,$ReqGrp);
        $LecGrp = mysqli_fetch_array($TabGrp);
        mysqli_free_result($TabGrp);
        
        $idGroupe = $LecGrp["idGroupe"];
        $nomGroupe = $LecGrp["nomGroupe"];
        $idProjet = $LecGrp["idProjet"];
        $nomProjet = $LecGrp["nomProjet"];
        $idModule = $LecGrp["idModule"];
        $nomModule = $LecGrp["nomModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["groupe"]) || $LecGrp == NULL)
    {
?>
        <script>
            window.location.href = "../../../accueilGestion.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../../images/amelia.ico" />
        <title>AMÉLIA – <?php echo $nomGroupe; ?></title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr class="header">
                <td class="header"><a href="../../../accueilGestion.php">Accueil</a> > <a href="../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <?php echo $nomGroupe; ?></td>
                <td class="right"><a href="../projet.php?projet=<?php echo $idProjet; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Supprimer un groupe</h2>
            
            <article>
                <form method="POST">
                    <p>Expliquer brièvement la raison de votre suppression :</p>
                    <textarea name="_justif" rows=5></textarea>
                    <table class="large">
                        <tr>
                            <td>Voulez-vous vraiment supprimer le groupe ?</td>
                            <td class="right"><input type="submit" name="_envoyer" value="Oui" class="vert"/> <input type="submit" name="_envoyer" value="Non" class="orange"/></td>
                        </tr>
                    </table>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_envoyer"]))
    {
        // Cas où le gestionnaire veut vraiment supprimer le groupe
        if($_POST["_envoyer"] == "Oui")
        {
            // Gestion des apostrophes
            $justif = $_POST["_justif"];
            $justif = str_replace("'","\'",$justif);
            
            // Récupération du login du propriétaire du groupe qu’on supprime
            $ReqLog = "SELECT * FROM APPARTIENT, GROUPE WHERE admin = 2 AND idGroupeAp = idGroupe AND idGroupe = ".$idGroupe;
            $TabLog = mysqli_query($BDD,$ReqLog);
            $LecLog = mysqli_fetch_array($TabLog);
            mysqli_free_result($TabLog);
            
            // Envoi du message de justification du refus
            $ReqJstf = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."','".$LecLog["loginEleveAp"]."','Suppression de votre groupe ".$nomGroupe."','".$justif."')";
            
            // Suppression du groupe
            $ReqMembr = "DELETE FROM APPARTIENT WHERE idGroupeAp = ".$idGroupe;
            $ReqRendu = "DELETE FROM RENDU WHERE idGroupeRe = ".$idGroupe;
            $ReqCandi = "DELETE FROM CANDIDATURE WHERE idGroupeCa = ".$idGroupe;
            $ReqClien = "DELETE FROM GERE WHERE idGroupeGe = ".$idGroupe;
            $ReqSuppr = "DELETE FROM GROUPE WHERE idGroupe = ".$idGroupe;
            
            // Suppression des rendus sur le serveur
            $ReqServr = "SELECT * FROM RENDU WHERE idGroupeRe = ".$LecGrp["idGroupe"];
            $TabServr = mysqli_query($BDD,$ReqServr);
            
            while($LecServr = mysqli_fetch_array($TabServr))
            {
                unlink("../../../../rendu/".$LecServr["urlRendu"]);
            }
            
            mysqli_free_result($TabServr);
            
            if(mysqli_query($BDD,$ReqClien) && mysqli_query($BDD,$ReqCandi) && mysqli_query($BDD,$ReqRendu) && mysqli_query($BDD,$ReqMembr) && mysqli_query($BDD,$ReqSuppr) && mysqli_query($BDD,$ReqJstf))
            {
?>
                <script>
                    alert("Votre suppression a bien été enregistrée !");
                    window.location.href = "../projet.php?projet=<?php echo $idProjet; ?>";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre suppression n’a pas pu être enregistrée.\nVeuillez réessayer.");
                    window.location.href = "supprimerGroupe.php?groupe=<?php echo $idGroupe; ?>";
                </script>
<?php
            }
        }
        
        // Cas où le gestionnaire se ravise
        else 
        {
?>
                <script>
                    window.location.href = "../projet.php?projet=<?php echo $idProjet; ?>";
                </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>