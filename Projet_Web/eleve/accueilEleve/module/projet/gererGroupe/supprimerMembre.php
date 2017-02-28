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
    
    // Récupération des identifiants et des noms du groupe, du projet et du module auquel appartient le membre
    if(isset($_GET["groupe"]) && isset($_GET["membre"]))
    {
        $ReqGrp = "SELECT * FROM APPARTIENT, GROUPE, PROJET, MODULE WHERE anneeModule LIKE '%".$_SESSION["_annee"]."%' AND idModule = idModulePere AND idProjet = idProjetGr AND idGroupe = idGroupeAp AND loginEleveAp = '".$_GET["membre"]."' AND admin <> 2 AND loginEleveAp <> '".$_COOKIE["_idf"]."' AND idGroupeAp = ".$_GET["groupe"];
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
    if(!isset($_GET["groupe"]) || !isset($_GET["membre"]) || $LecGrp == NULL)
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
        <title>AMÉLIA – Supprimer un membre</title>
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
                <td class="header"><a href="../../../../accueilEleve.php">Accueil</a> > <a href="../../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>"><?php echo $nomGroupe; ?></a> > Supprimer un membre</td>
                <td class="right"><a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Supprimer un membre</h2>
            
            <article>
                <form method="POST">
                    <p>Expliquer brièvement la raison de votre suppression :</p>
                    <textarea name="_justif" rows=5></textarea>
                    <p>Voulez-vous vraiment supprimer le membre ?</td>
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
    // Cas où l’élève veut vraiment supprimer le membre
    if(isset($_POST["_oui"]))
    {
        // Gestion des apostrophes
        $justif = $_POST["_justif"];
        $justif = str_replace("'","’",$justif);
        
        // Envoi du message de justification de suppression
        $ReqJstf = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."','".$_GET["membre"]."','Suppression du groupe ".$nomGroupe."','".$justif."')";
        
        // Suppression du membre au groupe
        $ReqAdm = "DELETE FROM APPARTIENT WHERE loginEleveAp = '".$_GET["membre"]."' AND idGroupeAp = ".$_GET["groupe"];
        
        if(mysqli_query($BDD,$ReqAdm) && mysqli_query($BDD,$ReqJstf))
        {
?>
            <script>
                alert("Votre requête a bien été exécutée !");
                window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
        }
        
        else 
        {
?>
            <script>
                alert("Erreur ! Votre requête n’a pas pu être exécutée.\nVeuillez réessayer.");
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