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
        $ReqMdl = "SELECT * FROM MODULE WHERE loginEnseiResp = '".$_COOKIE["_idf"]."' AND idModule = ".$_GET["module"];
        $TabMdl = mysqli_query($BDD,$ReqMdl);
        $LecMdl = mysqli_fetch_array($TabMdl);
        mysqli_free_result($TabMdl);
    
        $nomModule = $LecMdl["nomModule"];
        $idModule = $LecMdl["idModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["module"]) || $LecMdl == NULL)
    {
?>
        <script>
            window.location.href = "../accueilEnseignant.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="../../stylesheet.css" />
        <link rel="shortcut icon" href="../../images/amelia.ico" />
        <title>
            AMÉLIA – <?php echo "Supprimer le module ".$nomModule; ?>
        </title>
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
        
        <!--Fil d'ariane-->
        <table class="header">
            <tr>
                <td><a href="../accueilEnseignant.php">Accueil</a> > Suppression de <?php echo $nomModule; ?></td>
                <td class="right"><a href="../accueilEnseignant.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2> Suppression de <?php echo $nomModule; ?></h2>  
            
            <article>
                <form method="POST">
                    <p>Êtes-vous sûr de vouloir supprimer le module <?php echo $nomModule ?> ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert"/> <input type="submit" name="_non" value="Non" class="orange"/></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Si l'enseignant accepte (appuie sur "Oui")
    if(isset($_POST["_oui"])) 
    {
        // Récupération des projets présents dans le module
        $ReqPro = "SELECT idProjet FROM PROJET WHERE idModulePere=".$idModule."";
        $TabPro = mysqli_query($BDD, $ReqPro);
        
        // Pour chaque projet :
        while($LecPro = mysqli_fetch_array($TabPro))
        {
            // Récupération des groupes du projet
            $ReqGr = "SELECT idGroupe FROM GROUPE WHERE idProjetGr='".$LecPro['idProjet']."'";
            $TabGr = mysqli_query($BDD, $ReqGr);
            
            // Pour chaque groupe :
            while($LecGr = mysqli_fetch_array($TabGr))
            {
                // Suppression des documents sur le serveur
                $ReqDocServr = "SELECT * FROM RENDU WHERE idGroupeRe='".$LecGr['idGroupe']."'";
                $TabDocServr = mysqli_query($BDD,$ReqDocServr);
                while($LecDocServr = mysqli_fetch_array($TabDocServr))
                {
                    unlink("../../rendu/".$LecDocServr["urlRendu"]);
                }
                // Suppression des documents rendus liés au groupe
                $ReqSupprDoc = "DELETE FROM RENDU WHERE idGroupeRe='".$LecGr['idGroupe']."'";
                mysqli_query($BDD, $ReqSupprDoc);
                                
                // Suppression des candidatures liées au groupe
                $ReqSupprCandi = "DELETE FROM CANDIDATURE WHERE idGroupeCa='".$LecGr['idGroupe']."'";
                mysqli_query($BDD, $ReqSupprCandi);
                
                // Suppression de la liaison entre le groupe et le projet
                $ReqSupprAppart = "DELETE FROM appartient WHERE idGroupeAp='".$LecGr['idGroupe']."'";
                mysqli_query($BDD, $ReqSupprAppart);
                
                // Suppression de la liaison entre le client et le groupe : gere
                $ReqSupprGere = "DELETE FROM gere WHERE idGroupeGe='".$LecGr['idGroupe']."'";
                mysqli_query($BDD, $ReqSupprGere);
            }
            
            mysqli_free_result($TabGr);
            
            // Suppression des annexes sur le serveur
            $ReqAnServr = "SELECT * FROM ANNEXE WHERE idProjetAn='".$LecPro['idProjet']."'";
            $TabAnServr = mysqli_query($BDD,$ReqAnServr);
            while($LecAnServr = mysqli_fetch_array($TabAnServr))
            {
                unlink("../../annexe/".$LecAnServr["urlAnnexe"]);
            }
            // Suppression des annexes
            $ReqSupprAn = "DELETE FROM ANNEXE WHERE idProjetAn='".$LecPro['idProjet']."'";
            mysqli_query($BDD, $ReqSupprAn);
            
            // Suppression du groupe
            $ReqSupprGr = "DELETE FROM GROUPE WHERE idProjetGr='".$LecPro['idProjet']."'";
            mysqli_query($BDD, $ReqSupprGr);
        }
        
        mysqli_free_result($TabPro);
        
        // Suppression des projets
        $ReqSupprPro = "DELETE FROM PROJET WHERE idModulePere='".$idModule."'";
        mysqli_query($BDD, $ReqSupprPro);
        
        // Suppression du module
        $ReqSupprModu="DELETE FROM MODULE WHERE idModule='".$idModule."'";
        
        if(mysqli_query($BDD, $ReqSupprModu))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers la page d'accueil
            ?>
            <script> alert("<?php echo htmlspecialchars('Le module a bien été supprimé !', ENT_QUOTES); ?>");
            window.location.href="../accueilEnseignant.php";</script>
            <?php
        }
        
        else 
        {
            // Message d'alerte
            ?>
                <script>alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                window.location.href="supprimerModule.php?module=<?php echo $idModule; ?>";</script>
            <?php
        }
    }
    
    // Si l'enseignant refuse (appuie sur "Non")
    elseif(isset($_POST["_non"]))
    {
        // Redirection vers la page d'accueil
        header("Location:../accueilEnseignant.php");
    }
?>