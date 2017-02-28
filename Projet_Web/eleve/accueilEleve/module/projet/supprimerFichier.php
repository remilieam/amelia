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
    
    // Récupération des identifiants et des noms du groupe, du projet et du module auquel appartient le groupe
    if(isset($_GET["groupe"]))
    {
        $ReqGrp = "SELECT * FROM APPARTIENT, GROUPE, PROJET, MODULE WHERE anneeModule LIKE '%".$_SESSION["_annee"]."%' AND idModule = idModulePere AND idProjet = idProjetGr AND idGroupe = ".$_GET["groupe"]." AND idGroupe = idGroupeAp AND loginEleveAp = '".$_COOKIE["_idf"]."' AND admin <> 0";
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
            window.location.href = "../../../accueilEleve.php";
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
                <td class="header"><a href="../../../accueilEleve.php">Accueil</a> > <a href="../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <?php echo $nomGroupe; ?></td>
                <td class="right"><a href="../projet.php?projet=<?php echo $idProjet; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Supprimer des documents au groupe</h2>
            
            <article>
<?php
    // Récupération de tous les documents remis par le groupe
    $ReqDoc = "SELECT * FROM RENDU WHERE idGroupeRe = ".$idGroupe;
    $TabDoc = mysqli_query($BDD,$ReqDoc);
    
    // Vérification pour savoir s’il y a des documents à supprimer
    if(mysqli_num_rows($TabDoc) != 0)
    {
?>
                <form method="POST">
                    <p>Choisir les documents à supprimer :</p>
<?php
        $i = 0;
        
        while($LecDoc = mysqli_fetch_array($TabDoc))
        {
?>
                    <p><input type="checkbox" name="doc_<?php echo $i; ?>" value="<?php echo $LecDoc["idRendu"]; ?>" /> <?php echo $LecDoc["nomRendu"]; ?></p>
<?php
            $i += 1;
        }
?>
                    <p class="right"><input type="submit" name="supprimer" value="Supprimer" class="orange" /></p>
                </form>
<?php
    }
    
    else 
    {
?>
                    <p>Aucun document remis…</p>
<?php
    }
    
    mysqli_free_result($TabDoc);
?>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia – <a href="../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if (isset($_POST["supprimer"]))
    {
        $Nb = 0;
        
        // Récupération de tous les documents cochés dans un tableau
        for($j = 0; $j < $i; $j++)
        {
            if(isset($_POST["doc_$j"]))
            {
                $Tabl[$Nb] = $_POST["doc_$j"];
                $Nb += 1;
            }
        }
        
        $k = 0;
        
        // Suppression du groupe des documents cochés
        for($k = 0; $k < $Nb; $k++)
        {
            // Récupération de l’URL du document
            $ReqSup = "SELECT * FROM RENDU WHERE idRendu = ".$Tabl[$k];
            $TabSup = mysqli_query($BDD,$ReqSup);
            $LecSup = mysqli_fetch_array($TabSup);
            mysqli_free_result($TabSup);
            
            // Suppresion du document de la base de données
            $ReqSpr = "DELETE FROM RENDU WHERE idRendu = ".$Tabl[$k];
            
            // Suppression du document sur le serveur
            $Suppr = "../../../../rendu/".$LecSup["urlRendu"];
            
            if(unlink("$Suppr") && mysqli_query($BDD,$ReqSpr))
            {
?>
                <script>
                    alert("Votre document a bien été supprimé !");
                    window.location.href = "../projet.php?projet=<?php echo $idProjet; ?>";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre document n’a pas pu être supprimé.\nVeuillez réessayer.");
                    window.location.href = "supprimerFichier.php?groupe=<?php echo $idGroupe; ?>";
                </script>
<?php
            }
        }
    }
    
    mysqli_close($BDD);
?>