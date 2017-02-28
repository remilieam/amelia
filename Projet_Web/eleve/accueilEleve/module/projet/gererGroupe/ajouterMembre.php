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
    
    // Récupération des identifiants et des noms du groupe, du projet et du module auquel on veut ajouter un membre
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
        <title>AMÉLIA – Ajouter un membre</title>
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
                <td class="header"><a href="../../../../accueilEleve.php">Accueil</a> > <a href="../../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>"><?php echo $nomGroupe; ?></a> > Ajouter un membre</td>
                <td class="right"><a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Ajouter un membre</h2>
            
            <article>
                <form method="POST">
                    <p>
                        Cocher les membres que vous voulez ajouter au groupe :
                    </p>
<?php
    // Récupération de l’ensemble des élèves de la même année que l’élève, et n’appartenant pas encore à un groupe
    $ReqMbr = "SELECT * FROM CONNEXION WHERE login <> '".$_COOKIE["_idf"]."' AND anneeEleve = (SELECT anneeEleve FROM CONNEXION WHERE login = '".$_COOKIE["_idf"]."') AND login NOT IN (SELECT loginEleveAp FROM APPARTIENT, GROUPE, PROJET WHERE idGroupeAP = idGroupe AND idProjetGr = idProjet AND idProjet = ".$idProjet.")";
    $TabMbr = mysqli_query($BDD,$ReqMbr);
    
    // Vérification qu’il y a des élèves que l’on peut ajouter au groupe
    if(mysqli_num_rows($TabMbr) != 0)
    {
        $i = 0;
        
        // Si oui, on affiche les élèves
        while($LecMbr = mysqli_fetch_array($TabMbr))
        {
?>
                    <p><input type="checkbox" name="_membre_<?php echo $i; ?>" value="<?php echo $LecMbr["login"]; ?>"/> <?php echo $LecMbr["prenom"]." ".$LecMbr["nom"]; ?></p>
<?php
            $i += 1;
        }
    }
    
    else 
    {
?>
                    <p>Aucun élève ne peut être ajouté au groupe…</p>
<?php
    }
    
    mysqli_free_result($TabMbr);
?>
                    <p class="right">
                        <input type="submit" name="_ajouter" value="Ajouter" class="vert" />
                    </p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia – <a href="../../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_ajouter"]))
    {
        $Nb = 0;
        
        // Récupération des élèves cochés dans un tableau
        for($j = 0; $j < $i; $j++)
        {
            if(isset($_POST["_membre_$j"]))
            {
                $Eleve[$Nb] = $_POST["_membre_$j"];
                $Nb += 1;
            }
        }
        
        // Ajout des élèves cochés dans le groupe
        for($k = 0; $k < $Nb; $k++)
        {
            $ReqElv = "INSERT INTO APPARTIENT (loginEleveAp,idGroupeAp) VALUES ('".$Eleve[$k]."',".$idGroupe.")";
            
            // En cas d’échec de l’ajout d’un des futurs membres, on annule toutes les actions précédentes
            if(!mysqli_query($BDD,$ReqElv)) 
            {
                // Suppression de tous les élèves déjà ajoutés
                for($l = 0; $l < $k; $l++)
                {
                    $ReqSpr = "DELETE FROM APPARTIENT WHERE idGroupeAp = ".$idGroupe;
                }
?>
                <script>
                    alert("Erreur ! Votre requête n’a pas pu être effectuée :\nTous les élèves n’ont pas pu être ajoutés dans le groupe.\nVeuillez réessayer.");
                    window.location.href = "ajouterMembre.php?groupe=<?php echo $idGroupe; ?>";
                </script>
<?php
            }
        }
?>
        <script>
            alert("Votre requête a bien été effectuée !");
            window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
        </script>
<?php
    }
    
    mysqli_close($BDD);
?>
