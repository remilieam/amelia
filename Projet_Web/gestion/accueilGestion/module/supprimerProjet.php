<?php
    require("../../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../../connexion.php");
    }
    
    // Récupération des identifiants et des noms du projet et du module auquel appartient le projet
    if(isset($_GET["projet"]))
    {
        $ReqPrj = "SELECT * FROM PROJET, MODULE WHERE idModule = idModulePere AND idProjet = ".$_GET["projet"];
        $TabPrj = mysqli_query($BDD,$ReqPrj);
        $LecPrj = mysqli_fetch_array($TabPrj);
        mysqli_free_result($TabPrj);
    
        $idProjet = $LecPrj["idProjet"];
        $nomProjet = $LecPrj["nomProjet"];
        $idModule = $LecPrj["idModule"];
        $nomModule = $LecPrj["nomModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["projet"]) || $LecPrj == NULL)
    {
?>
        <script>
            window.location.href = "../../accueilGestion.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../images/amelia.ico" />
        <title>AMÉLIA – <?php echo $nomProjet; ?></title>
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
            <tr class="header">
                <td class="header"><a href="../../accueilGestion.php">Accueil</a> > <a href="../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <?php echo $nomProjet; ?></td>
                <td class="right"><a href="../module.php?module=<?php echo $idModule; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Supprimer un projet</h2>
            
            <article>
                <form method="POST">
                    <p>Expliquer brièvement la raison de votre suppression :</p>
                    <textarea name="_justif" rows=5></textarea>
                    <table class="large">
                        <tr>
                            <td>Voulez-vous vraiment supprimer le projet ?</td>
                            <td class="right"><input type="submit" name="_envoyer" value="Oui" class="vert"/> <input type="submit" name="_envoyer" value="Non" class="orange"/></td>
                        </tr>
                    </table>
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
        // Cas où le gestionnaire veut vraiment supprimer le projet
        if($_POST["_envoyer"] == "Oui")
        {
            // Gestion des apostrophes
            $justif = $_POST["_justif"];
            $justif = str_replace("'","\'",$justif);
            
            // Récupération du login du responsable auquel appartient le projet qu’on supprime
            $ReqLog = "SELECT * FROM PROJET, MODULE WHERE idModule = idModulePere AND idProjet = ".$idProjet;
            $TabLog = mysqli_query($BDD,$ReqLog);
            $LecLog = mysqli_fetch_array($TabLog);
            mysqli_free_result($TabLog);
            
            // Envoi du message de justification du refus
            $ReqJstf = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."','".$LecLog["loginEnseiResp"]."','Suppression de votre projet ".$nomProjet."','".$justif."')";
            
            // Récupération et suppression des membres des groupes au sein du projet, avec leurs rendus
            $ReqGrp = "SELECT * FROM GROUPE WHERE idProjetGr = ".$idProjet;
            $TabGrp = mysqli_query($BDD,$ReqGrp);
            
            while($LecGrp = mysqli_fetch_array($TabGrp))
            {
                // Suppression des rendus sur le serveur
                $ReqServr = "SELECT * FROM RENDU WHERE idGroupeRe = ".$LecGrp["idGroupe"];
                $TabServr = mysqli_query($BDD,$ReqServr);
                
                while($LecServr = mysqli_fetch_array($TabServr))
                {
                    unlink("../../../rendu/".$LecServr["urlRendu"]);
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
            }
            
            mysqli_free_result($TabGrp);
            
            // Suppression du projet => Suppression de tous les groupes au sein du projet => Suppression des annexes
            $ReqGroup = "DELETE FROM GROUPE WHERE idProjetGr = ".$idProjet;
            $ReqSuppr = "DELETE FROM PROJET WHERE idProjet = ".$idProjet;
            $ReqAnnex = "DELETE FROM ANNEXE WHERE idProjetAn = ".$idProjet;
            
            // Suppression des annexes sur le serveur
            $ReqServe = "SELECT * FROM ANNEXE WHERE idProjetAn = (SELECT idProjet FROM PROJET WHERE idModulePere = ".$idModule.")";
            $TabServe = mysqli_query($BDD,$ReqServe);
            
            while($LecServe = mysqli_fetch_array($TabServe))
            {
                unlink("../../../annexe/".$LecServr["urlAnnexe"]);
            }
            
            mysqli_free_result($TabServe);
            
            if(mysqli_query($BDD,$ReqGroup) && mysqli_query($BDD,$ReqAnnex) && mysqli_query($BDD,$ReqSuppr) && mysqli_query($BDD,$ReqJstf))
            {
?>
                <script>
                    alert("Votre suppression a bien été enregistrée !");
                    window.location.href = "../module.php?module=<?php echo $idModule; ?>";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre suppression n’a pas pu être enregistrée.\nVeuillez réessayer.");
                    window.location.href = "supprimerProjet.php?projet=<?php echo $idProjet; ?>";
                </script>
<?php
            }
        }
        
        // Cas où le gestionnaire se ravise
        else 
        {
?>
                <script>
                    window.location.href = "../module.php?module=<?php echo $idModule; ?>";
                </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>