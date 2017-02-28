<!DOCTYPE html>

<?php
    require("../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../connexion.php");
    }
    
    // Récupération de l’identifiant et du nom du module
    if(isset($_GET["module"]))
    {
        $ReqMdl = "SELECT * FROM MODULE WHERE idModule = ".$_GET["module"];
        $TabMdl = mysqli_query($BDD,$ReqMdl);
        $LecMdl = mysqli_fetch_array($TabMdl);
        mysqli_free_result($TabMdl);
    
        $idModule = $LecMdl["idModule"];
        $nomModule = $LecMdl["nomModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["module"]) || $LecMdl == NULL)
    {
?>
        <script>
            window.location.href = "../accueilGestion.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="../../stylesheet.css" />
        <link rel="shortcut icon" href="../../images/amelia.ico" />
        <title>AMÉLIA – <?php echo $nomModule; ?></title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr class="header">
                <td class="header"><a href="../accueilGestion.php">Accueil</a> > <?php echo $nomModule; ?></td>
                <td class="right"><a href="module.php?module=<?php echo $idModule; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Supprimer un module</h2>
            
            <article>
                <form method="POST">
                    <p>Expliquer brièvement la raison de votre suppression :</p>
                    <textarea name="_justif" rows=5></textarea>
                    <table class="large">
                        <tr>
                            <td>Voulez-vous vraiment supprimer le module ?</td>
                            <td class="right"><input type="submit" name="_envoyer" value="Oui" class="vert"/> <input type="submit" name="_envoyer" value="Non" class="orange"/></td>
                        </tr>
                    </table>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_envoyer"]))
    {
        // Cas où le gestionnaire veut vraiment supprimer le projet
        if($_POST["_envoyer"] == "Oui")
        {
            // Gestion des apostrophes
            $justif = $_POST["_justif"];
            $justif = str_replace("'","\'",$justif);
            
            // Récupération du login du responsable auquel appartient le projet qu’on supprime
            $ReqLog = "SELECT * FROM PROJET, MODULE WHERE idModule = ".$idModule;
            $TabLog = mysqli_query($BDD,$ReqLog);
            $LecLog = mysqli_fetch_array($TabLog);
            mysqli_free_result($TabLog);
            
            // Envoi du message de justification du refus
            $ReqJstf = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."','".$LecLog["loginEnseiResp"]."','Suppression de votre module ".$nomModule."','".$justif."')";
            
            // Récupération et suppression des groupes, de ses membres et de ses rendus, au sein du module
            $ReqGrp = "SELECT * FROM GROUPE, PROJET, MODULE WHERE idProjetGr = idProjet AND idModulePere = idModule AND idModule = ".$idModule;
            $TabGrp = mysqli_query($BDD,$ReqGrp);
            
            while($LecGrp = mysqli_fetch_array($TabGrp))
            {
                // Suppression des rendus sur le serveur
                $ReqServr = "SELECT * FROM RENDU WHERE idGroupeRe = ".$LecGrp["idGroupe"];
                $TabServr = mysqli_query($BDD,$ReqServr);
                
                while($LecServr = mysqli_fetch_array($TabServr))
                {
                    unlink("../../rendu/".$LecServr["urlRendu"]);
                }
                
                mysqli_free_result($TabServr);
                
                $ReqMembr = "DELETE FROM APPARTIENT WHERE idGroupeAp = ".$LecGrp["idGroupe"];
                mysqli_query($BDD,$ReqMembr);
                $ReqRendu = "DELETE FROM RENDU WHERE idGroupeRe = ".$LecGrp["idGroupe"];
                mysqli_query($BDD,$ReqRendu);
                $ReqCandi = "DELETE FROM CANDIDATURE WHERE idGroupeCa = ".$LecGrp["idGroupe"];
                mysqli_query($BDD,$ReqCandi);
                $ReqClien = "DELETE FROM GERE WHERE idGroupeGe = ".$LecGrp["idGroupe"];
                mysqli_query($BDD,$ReqClien);
                $ReqGroup = "DELETE FROM GROUPE WHERE idGroupe = ".$LecGrp["idGroupe"];
                mysqli_query($BDD,$ReqGroup);
            }
            
            mysqli_free_result($TabGrp);
            
            // Suppression du module => Suppression de tous les projets
            $ReqAnnex = "DELETE FROM ANNEXE WHERE idProjetAn = (SELECT idProjet FROM PROJET WHERE idModulePere = ".$idModule.")";
            $ReqProje = "DELETE FROM PROJET WHERE idModulePere = ".$idModule;
            $ReqSuppr = "DELETE FROM MODULE WHERE idModule = ".$idModule;
            
            // Suppression des annexes sur le serveur
            $ReqServe = "SELECT * FROM ANNEXE WHERE idProjetAn = (SELECT idProjet FROM PROJET WHERE idModulePere = ".$idModule.")";
            $TabServe = mysqli_query($BDD,$ReqServe);
            
            while($LecServe = mysqli_fetch_array($TabServe))
            {
                unlink("../../annexe/".$LecServr["urlAnnexe"]);
            }
            
            mysqli_free_result($TabServe);
            
            if(mysqli_query($BDD,$ReqAnnex) && mysqli_query($BDD,$ReqProje) && mysqli_query($BDD,$ReqSuppr) && mysqli_query($BDD,$ReqJstf))
            {
?>
                <script>
                    alert("Votre suppression a bien été enregistrée !");
                    window.location.href = "../accueilGestion.php";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre suppression n’a pas pu être enregistrée.\nVeuillez réessayer.");
                    window.location.href = "supprimerModule.php?module=<?php echo $idModule; ?>";
                </script>
<?php
            }
        }
        
        // Cas où le gestionnaire se ravise
        else 
        {
?>
                <script>
                    window.location.href = "../accueilGestion.php";
                </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>