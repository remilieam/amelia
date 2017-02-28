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
            
            <h2>Ajouter un document au groupe</h2>
            
            <article>
                <form method="POST" enctype="multipart/form-data">
                    <p>Entrer le nom que vous désirez donner à votre document : <input type="text" name="_nom" /></p>
                    <p>Choisir le document à ajouter : <input type="file" name="_fichier" /></p>
                    <p class="right"><input type="submit" name="_ajouter" value="Ajouter" class="vert" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_FILES["_fichier"]) AND $_FILES["_fichier"]["error"] == 0)
    {
        $urlFichier = $_FILES["_fichier"]["name"];
        $nomFichier = str_replace("'","’",$urlFichier);
        
        // Cas où l’utilisateur spécifie un nouveau nom
        if($_POST["_nom"] != "")
        {
            $nomFichier = str_replace("'","’",$_POST["_nom"]);
        }
        
        // Vérification que le document n’existe pas déjà
        $ReqRen = "SELECT * FROM RENDU WHERE urlRendu = '".$urlFichier."'";
        $TabRen = mysqli_query($BDD,$ReqRen);
        $LecRen = mysqli_fetch_array($TabRen);
        mysqli_free_result($TabRen);
        
        // Cas où un document porte le même nom
        if($LecRen != NULL)
        {
            $ReqNom = "UPDATE RENDU SET urlRendu = '".$LecRen["idRendu"]."_".$urlFichier."' WHERE urlRendu = '".$urlFichier."'";
            mysqli_query($BDD,$ReqNom);
            rename("../../../../rendu/".$urlFichier,"../../../../rendu/".$LecRen["idRendu"]."_".$urlFichier);
        }
        
        // Ajout du fichier à la base de données
        $ReqDoc = "INSERT INTO RENDU (nomRendu, urlRendu, idGroupeRe) VALUES ('".$nomFichier."','".$urlFichier."',".$idGroupe.")";
        
        // Ajout du fichier sur le serveur
        if(move_uploaded_file($_FILES["_fichier"]["tmp_name"],"../../../../rendu/".basename($urlFichier)) && mysqli_query($BDD,$ReqDoc))
        {
?>
            <script>
                alert("Votre document a bien été envoyé !");
                window.location.href = "../projet.php?projet=<?php echo $idProjet; ?>";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre document n’a pas pu être envoyé.\nVeuillez réessayer.");
                window.location.href = "ajouterFichier.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>
