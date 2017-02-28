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
    
    // Récupération des identifiants et des noms du groupe, du projet et du module à supprimer
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
        <title>AMÉLIA – Supprimer un groupe</title>
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
                <td class="header"><a href="../../../../accueilEleve.php">Accueil</a> > <a href="../../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>"><?php echo $nomGroupe; ?></a> > Supprimer un groupe</td>
                <td class="right"><a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Supprimer un groupe</h2>
            
            <article>
                <form method="POST">
                    <p>Voulez-vous vraiment supprimer le groupe ?</td>
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
    // Cas où l’élève veut vraiment supprimer le groupe
    if(isset($_POST["_oui"]))
    {
        // Suppression du groupe par étape
        $ReqMembr = "DELETE FROM APPARTIENT WHERE idGroupeAp = ".$idGroupe;
        $ReqSuppr = "DELETE FROM GROUPE WHERE idGroupe = ".$idGroupe;
        $ReqCandi = "DELETE FROM CANDIDATURE WHERE idGroupeCa = ".$idGroupe;
        
        if(mysqli_query($BDD,$ReqCandi) && mysqli_query($BDD,$ReqMembr) && mysqli_query($BDD,$ReqSuppr))
        {
?>
            <script>
                alert("Votre suppression a bien été enregistrée !");
                window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre suppression n’a pas pu être enregistrée.\nVeuillez réessayer.");
                window.location.href = "supprimerMembre.php?groupe=membre=<?php echo $_GET["groupe"]; ?>&<?php echo $_GET["membre"]; ?>";
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