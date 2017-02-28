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
    
    // Récupération de l’identifiant et du nom du module
    if(isset($_GET["projet"]))
    {
        $ReqPrj = "SELECT * FROM PROJET, MODULE WHERE idModule = idModulePere AND loginEnseiResp = '".$_COOKIE["_idf"]."' AND idProjet = ".$_GET["projet"];
        $TabPrj = mysqli_query($BDD,$ReqPrj);
        $LecPrj = mysqli_fetch_array($TabPrj);
        mysqli_free_result($TabPrj);
        
        $nomProjet = $LecPrj["nomProjet"];
        $idProjet = $LecPrj["idProjet"];
        $nomModule = $LecPrj["nomModule"];
        $idModule = $LecPrj["idModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["projet"]) || $LecPrj == NULL)
    {
?>
        <script>
            window.location.href = "../../accueilEnseignant.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../images/amelia.ico" />
        <title>
            AMÉLIA – Supprimer le projet <?php echo $nomProjet; ?>
        </title>
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
        
        <!--Fil d'ariane-->
        <table class="header">
            <tr>
                <td><a href="../../accueilEnseignant.php">Accueil</a> > <a href='../module.php?module=<?php echo $idModule; ?>'><?php echo $nomModule; ?></a> > Suppression de <?php echo $nomProjet; ?></td>
                <td class="right"><a href='../module.php?module=<?php echo $idModule; ?>' class='blanc'>Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2> Suppression de <?php echo $nomProjet; ?></h2>  
            
            <article>
                <form method="POST">
                    <p>Êtes-vous sûr de vouloir supprimer <?php echo $nomProjet; ?> ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert"/> <input type="submit" name="_non" value="Non" class="orange"/></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Si l'enseignant accepte (appuie sur "Oui")
    if(isset($_POST["_oui"])) 
    {
        // Récupération des groupes du projet
        $ReqGr = "SELECT idGroupe FROM GROUPE WHERE idProjetGr='$idProjet'";
        $TabGr = mysqli_query($BDD, $ReqGr);
        
        // Pour chaque groupe :
        while($LecGr = mysqli_fetch_array($TabGr))
        {
            // Suppression des documents sur le serveur
            $ReqDocServr = "SELECT * FROM RENDU WHERE idGroupeRe='".$LecGr['idGroupe']."'";
            $TabDocServr = mysqli_query($BDD,$ReqDocServr);
            while($LecDocServr = mysqli_fetch_array($TabDocServr))
            {
                unlink("../../../rendu/".$LecDocServr["urlRendu"]);
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
        
        // Suppression des annexes sur le serveur
        $ReqAnServr = "SELECT * FROM ANNEXE WHERE idProjetAn='".$_GET['projet']."'";
        $TabAnServr = mysqli_query($BDD,$ReqAnServr);
        while($LecAnServr = mysqli_fetch_array($TabAnServr))
        {
            unlink("../../../annexe/".$LecAnServr["urlAnnexe"]);
        }
        // Suppression des annexes
        $ReqSupprAn = "DELETE FROM ANNEXE WHERE idProjetAn='".$_GET['projet']."'";
        mysqli_query($BDD, $ReqSupprAn);
        
        
        
        // Suppression des groupes
        $ReqSupprGr="DELETE FROM GROUPE WHERE idProjetGr='".$_GET['projet']."'";
        mysqli_query($BDD, $ReqSupprGr);
        
        // Suppression du projet
        $ReqSupprPro="DELETE FROM PROJET WHERE idProjet='".$_GET['projet']."'";
        
        if(mysqli_query($BDD, $ReqSupprPro))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers la page du module
            ?>
            <script> alert("<?php echo htmlspecialchars('Le projet a bien été supprimé !', ENT_QUOTES); ?>");
            window.location.href="../module.php?module=<?php echo $idModule; ?>";</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
            <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
            window.location.href="supprimerProjet.php?projet=<?php echo $idProjet; ?>";</script>
            <?php
        }
    }
    
    // Si l'enseignant refuse (appuie sur "Non")
    elseif(isset($_POST["_non"]))
    {
        // Redirection et redirection vers la page du module
        header("location: ../module.php?module=$idModule");
    }
?>