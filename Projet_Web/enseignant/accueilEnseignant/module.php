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
            AMÉLIA – <?php echo $nomModule; ?>
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
                <td><a href="../accueilEnseignant.php">Accueil</a> > <?php echo $nomModule; ?></td>
                <td class="right"><a href="../accueilEnseignant.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <p class="right"><a class="vert" href='module/ajouterProjet.php?module=<?php echo $idModule; ?>'>Ajouter un projet</a></p>
            
            <?php
            
            // Récupération des projets liés au module XX
            $ReqPro = "SELECT nomProjet, idProjet FROM PROJET WHERE idModulePere='".$_GET['module']."'";
            $TabPro = mysqli_query($BDD, $ReqPro);
            
            if(mysqli_num_rows($TabPro) != 0)
            {
                // Affichage des projets
                while($LecPro = mysqli_fetch_array($TabPro))
                {
                    // Liens vers les modifications du projet
                    echo"<table class='large'><tr><td><h2><a href='module/projet.php?projet=".$LecPro["idProjet"]."'>".$LecPro["nomProjet"]."</a></h2></td>";
                    echo"<td class='right'><a class='bleu' href='module/docProjet.php?projet=".$LecPro["idProjet"]."'>Documentation</a> ";
                    echo"<a class='jaune' href='module/modifierProjet.php?projet=".$LecPro["idProjet"]."'>Modifier</a> ";
                    echo"<a class='orange' href='module/supprimerProjet.php?projet=".$LecPro["idProjet"]."'>Supprimer</a></td></tr></table>";
                ?>
                
                <article>
                <?php
                    // Récupération des informations liées au projet (durée, date, ett taille du groupe
                    $ReqInfo = "SELECT duree, dateLimite,tailleGpMin, tailleGpMax FROM PROJET WHERE idProjet='".$LecPro["idProjet"]."'";
                    $TabInfo =  mysqli_query($BDD, $ReqInfo);
                    $LecInfo = mysqli_fetch_array($TabInfo);
                    mysqli_free_result($TabInfo);
                    
                    // Affectation des informations liées au projet à des variables
                    $duree = $LecInfo["duree"]." semaines";
                    $date = $LecInfo["dateLimite"];
                    $effectif = $LecInfo["tailleGpMin"]."-".$LecInfo["tailleGpMax"]." élève(s) par groupe";
                    
                    if($LecInfo["duree"] == NULL)
                    {
                            $duree = "Inconnue";
                    }
                    
                    if($LecInfo["dateLimite"] == NULL)
                    {
                            $date = "Inconnue";
                    }
                    
                    if($LecInfo["tailleGpMin"] == $LecInfo["tailleGpMax"])
                    {
                            $effectif = $LecInfo["tailleGpMin"]." élève(s) par groupe";
                    }
                    
                    if($LecInfo["tailleGpMin"] == $LecInfo["tailleGpMax"] && $LecInfo["tailleGpMax"] == NULL)
                    {
                            $effectif = "Inconnu";
                    }
                    
                    // Récupération du nombre de groupes dans le projet            
                    $ReqNb = "SELECT COUNT(idGroupe) FROM GROUPE WHERE idProjetGr='".$LecPro["idProjet"]."' AND validation =2";
                    $TabNb = mysqli_query($BDD, $ReqNb);
                    $LecNb = mysqli_fetch_array($TabNb);
                    mysqli_free_result($TabNb);
                    
                    // Affectation du nombre de groupes à une variable
                    $nbGroupes = $LecNb["COUNT(idGroupe)"];
                    
                    if($nbGroupes == NULL)
                    {
                        $nbGroupes="0";
                    }
                    
                    // Affichage des informations 
                    echo"<table class='large'><tr><td>Durée : ".$duree."</td><td>Effectif : ".$effectif."</td></tr>";
                    echo"<tr><td>Date de remise : ".$date."</td><td>Nombre de groupe(s) : ".$nbGroupes." groupe(s)</td></tr></table>";
                ?>        
                </article>
                <?php
                }
            }
            
            // S'il n'y aucun projet dans le module
            else 
            {
                echo"<article><p>Vous n’avez créé aucun projet !</p></article>";
            }
            
            mysqli_free_result($TabPro);
            ?>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>