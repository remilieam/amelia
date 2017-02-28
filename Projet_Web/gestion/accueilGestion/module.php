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
        $ReqMdl = "SELECT * FROM MODULE WHERE idModule = ".$_GET["module"];
        $TabMdl = mysqli_query($BDD,$ReqMdl);
        $LecMdl = mysqli_fetch_array($TabMdl);
        mysqli_free_result($TabMdl);
    
        $idModule = $LecMdl["idModule"];
        $nomModule = $LecMdl["nomModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["module"]) || $LecMdl == NULL)
    {
?>
        <script>
            window.location.href = "../accueilGestion.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../../stylesheet.css" />
        <link rel="shortcut icon" href="../../images/amelia.ico" />
        <title>AMÉLIA – <?php echo $nomModule; ?></title>
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
        
        <table class="header">
            <tr>
                <td><a href="../accueilGestion.php">Accueil</a> > <?php echo $nomModule; ?></td>
                <td class="right"><a href="../accueilGestion.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
<?php
    // Récupération de l’ensemble des projets du module sélectionné
    $ReqPrj = "SELECT * FROM PROJET WHERE idModulePere = ".$idModule." ORDER BY nomProjet";
    $TabPrj = mysqli_query($BDD,$ReqPrj);
    
    // Vérification pour savoir s’il y a des projets
    if(mysqli_num_rows($TabPrj) != 0)
    {
        // Si oui, on les affiche
        while($LecPrj = mysqli_fetch_array($TabPrj)) // Affichage des projets
        {
            // Récupération du nombre de groupes dans le projet
            $RequtNb = "SELECT COUNT(*) AS NbGroupe FROM GROUPE WHERE idProjetGr = '".$LecPrj["idProjet"]."'";
            $TabNb = mysqli_query($BDD,$RequtNb);
            $LecNb = mysqli_fetch_array($TabNb);
            mysqli_free_result($TabNb);
            
            // Gestion des caractéristiques du projet
            $nb = $LecNb["NbGroupe"]." groupe(s)";
            $duree = $LecPrj["duree"]." semaine(s)";
            $date = $LecPrj["dateLimite"];
            $effectif = $LecPrj["tailleGpMin"]."-".$LecPrj["tailleGpMax"]." élève(s) par groupe";
            
            if($LecPrj["duree"] == NULL)
            {
                $duree = "Inconnue";
            }
            if($LecPrj["dateLimite"] == NULL)
            {
                $date = "Inconnue";
            }
            if($LecPrj["tailleGpMin"] == $LecPrj["tailleGpMax"])
            {
                $effectif = $LecPrj["tailleGpMin"]." élève(s) par groupe";
            }
            if($LecPrj["tailleGpMin"] == $LecPrj["tailleGpMax"] && $LecPrj["tailleGpMax"] == NULL)
            {
                $effectif = "Inconnu";
            }
?>
            
            <table class="large">
                <tr>
                    <td><h2><a href="module/projet.php?projet=<?php echo $LecPrj["idProjet"]; ?>"><?php echo $LecPrj["nomProjet"]; ?></a></h2></td>
                    <td class="right"><a href="module/supprimerProjet.php?projet=<?php echo $LecPrj["idProjet"]; ?>" class="orange">Supprimer le projet</a></tr>
            </table>
            
            <article>
                <table class="large">
                    <tr>
                        <td>Durée : <?php echo $duree; ?></td>
                        <td>Effectif : <?php echo $effectif; ?></td>
                    </tr>
                    <tr>
                        <td>Date de remise : <?php echo $date; ?></td>
                        <td>Nombre de groupe : <?php echo $nb; ?></td>
                    </tr>
                    <tr>
                        <td><a href="module/voirDocumentation.php?projet=<?php echo $LecPrj["idProjet"]; ?>">Documentation</a></td>
                        <td></td>
                    </tr>
                </table>
            </article>
<?php
        }
    }
    
    // Si non, on affique qu’il n’y a aucun projet
    else  
    {
?>
            
            <h2><?php echo $nomModule; ?></a></h2>
            
            <article>
                <p>Aucun projet dans ce module…</p>
            </article>
<?php
    }
    
    mysqli_free_result($TabPrj);
?>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    mysqli_close($BDD);
?>