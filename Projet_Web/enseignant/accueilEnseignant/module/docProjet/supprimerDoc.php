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
    
    // Récupération de l’identifiant et du nom du module
    if(isset($_GET["annexe"]))
    {
        $ReqAnx = "SELECT * FROM ANNEXE, PROJET, MODULE WHERE idModule = idModulePere AND idProjet = idProjetAn AND loginEnseiResp = '".$_COOKIE["_idf"]."' AND idAnnexe = ".$_GET["annexe"];
        $TabAnx = mysqli_query($BDD,$ReqAnx);
        $LecAnx = mysqli_fetch_array($TabAnx);
        mysqli_free_result($TabAnx);
        
        $nomAnnexe = $LecAnx["nomAnnexe"];
        $idAnnexe = $LecAnx["idAnnexe"];
        $urlAnnexe = $LecAnx["urlAnnexe"];
        $nomProjet = $LecAnx["nomProjet"];
        $idProjet = $LecAnx["idProjet"];
        $nomModule = $LecAnx["nomModule"];
        $idModule = $LecAnx["idModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["annexe"]) || $LecAnx == NULL)
    {
?>
        <script>
            window.location.href = "../../../accueilEnseignant.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="../../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../../images/amelia.ico" />
        <title>
            AMÉLIA – Supprimer l’annexe “<?php echo $nomAnnexe; ?>”
        </title>
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
        
        <!--Fil d'ariane-->
        <table class="header">
            <tr>
                <td><a href="../../../accueilEnseignant.php">Accueil</a> > <a href='../../module.php?module=<?php echo $idModule; ?>'><?php echo $nomModule; ?></a> > <a href='../projet.php?projet=<?php echo $idProjet; ?>'>Documents de <?php echo $nomProjet; ?></a> > Suppression de “<?php echo $nomAnnexe ?>”</td>
                <td class="right"><a href='../module.php?module=<?php echo $idModule; ?>' class='blanc'>Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Document “<?php echo $nomAnnexe ?>” de <?php echo $nomProjet ?></h2>  
            
            <article>
                <form method="POST">
                    <p>Êtes-vous sûr de vouloir supprimer “<?php echo $nomAnnexe ?>” ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert"/> <input type="submit" name="_non" value="Non" class="orange"/></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Si l'enseignant accepte (appuie sur "Oui")
    if(isset($_POST["_oui"])) 
    {
        // Suppression du document de la base de données
        $ReqSupprDoc="DELETE FROM ANNEXE WHERE idAnnexe='$idAnnexe'";
        
        // Suppression du document sur le serveur
        $Suppr = "../../../../annexe/$urlAnnexe";
        
        if(unlink("$Suppr") && mysqli_query($BDD, $ReqSupprDoc))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers la page de la documentation
            ?>
            <script> alert("<?php echo htmlspecialchars('Le document a bien été supprimé !', ENT_QUOTES); ?>");
            window.location.href="../docProjet.php?projet=<?php echo $idProjet; ?>";</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
            <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
            window.location.href="supprimerDoc.php?annexe=<?php echo $idAnnexe; ?>";</script>
            <?php
        }
    }
    
    // Si l'enseignant refuse (appuie sur "Non")
    elseif(isset($_POST["_non"]))
    {
        // Redirection et redirection vers la page des documents
        header("location: ../docProjet.php?projet=$idProjet");
    }
?>