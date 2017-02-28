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
            
            <h2>Modifier le nom du groupe</h2>
            
            <article>
                <form method="POST">
                    <p>Choisissez le nouveau nom de votre groupe : <input type="text" name="_nomGroupe"/></p>
                    <p class="right"><input type="submit" name="_valider1" value="Valider" class="vert" />
                </form>
            </article>
            
            <h2>Modifier la description du groupe</h2>
            
            <article>
                <form method="POST">
                    <p>Entrer la nouvelle description de votre groupe :</p>
                    <textarea name="_description" rows= 5></textarea>
                    <p class="right"><input type="submit" name="_valider2" value="Valider" class="vert"/>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Modification du nom du groupe
    if(isset($_POST["_valider1"]))
    {
        $ReqNom = "UPDATE GROUPE SET nomGroupe = '".str_replace("'","’",$_POST["_nomGroupe"])."' WHERE idGroupe = ".$idGroupe;
        
        if(mysqli_query($BDD,$ReqNom))
        {
?>
            <script>
                alert("Votre modification a bien été enregistrée !");
                window.location.href = "../projet.php?projet=<?php echo $idProjet; ?>";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre modification n’a pas pu être enregistrée.\nVeuillez réessayer.");
                window.location.href = "modifierGroupe.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
        }
    }
    
    // Modification de la description du groupe
    elseif(isset($_POST["_valider2"]))
    {
        // Gestion des apostrophes
        $description = $_POST["_description"];
        $description = str_replace("'","’",$description);
        
        $ReqDcr = "UPDATE GROUPE SET description = '".$description."' WHERE idGroupe = ".$idGroupe;
        
        if(mysqli_query($BDD,$ReqDcr))
        {
?>
            <script>
                alert("Votre modification a bien été enregistrée !");
                window.location.href = "../projet.php?projet=<?php echo $idProjet; ?>";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre modification n’a pas pu être enregistrée.\nVeuillez réessayer.");
                window.location.href = "modifierGroupe.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>
