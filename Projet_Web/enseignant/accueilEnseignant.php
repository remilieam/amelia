<!DOCTYPE html>

<?php 
    require ("../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    $identifiant = $_COOKIE["_idf"];
    $nom = $_SESSION["_nom"];
    $prenom = $_SESSION["_prenom"];
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../connexion.php");
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="../stylesheet.css" />
        <link rel="shortcut icon" href="../images/amelia.ico" />
        <title>
            AMÉLIA – Accueil
        </title>
    </head>
    
    <body>      
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr>
                <td>Accueil</td>
                <td class="right"></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Actualités</h2>
            
            <article>
        <?php
            // Récupération des projets où il faut valider/refuser les groupes
            $ReqRecupPr = "SELECT nomProjet, idProjet, COUNT(idGroupe) FROM PROJET, GROUPE, MODULE WHERE idProjetGr=idProjet AND validation=1 AND loginEnseiResp='".$identifiant."' AND idModule=idModulePere GROUP  BY idProjet";
            $TabRecupPr = mysqli_query($BDD, $ReqRecupPr);
            
            // S'il y a des groupes à valider/refuser
            if(mysqli_num_rows($TabRecupPr) != 0)
            {
                // Affichage des projets où il faut valider/refuser les groupes
                while($LecRecupPr = mysqli_fetch_array($TabRecupPr))
                {
                    echo "<p>".$LecRecupPr["COUNT(idGroupe)"]." groupe(s) à valider dans ".$LecRecupPr["nomProjet"]."</p>";
                    echo "<p class='right'><a class ='bleu' href='accueilEnseignant/voirGroupe.php?projet=".$LecRecupPr["idProjet"]."'>Voir</a></p>";
                }
            }
            
            // S'il n'y a aucun groupe à valider/refuser
            else 
            {
                echo "<p>Il n’y a aucun groupe à valider !</p>";
            }
            
            mysqli_free_result($TabRecupPr);
        ?>
            </article>
            
            <table class="large">
                <tr>
                    <td><h2>Modules</h2></td>
                    <td class="right"><a href='accueilEnseignant/ajouterModule.php' class="vert">Ajouter un module</a></td>
                </tr>
            </table>
            
            <article>
            <?php
            // Récupération des modules de l'enseignant et de l'année correspondant
            $ReqModu = "SELECT nomModule, idModule, anneeModule FROM MODULE WHERE loginEnseiResp='".$identifiant."'";
            $TabModu = mysqli_query($BDD, $ReqModu);
            
            // S'il y a l'enseignant est responsable d'un ou plusieurs modules
            if(mysqli_num_rows($TabModu) != 0)
            {
                // Affichage des modules de l'enseignant
                while($LecModu = mysqli_fetch_array($TabModu))
                {
                    // Lien vers les modules, la suppression du module et la modification du module
                    ?>
                    <table class='large'>
                        <tr>
                            <td><a href="accueilEnseignant/module.php?module=<?php echo $LecModu["idModule"]; ?>"><?php echo $LecModu["nomModule"]; ?></a></td>
                            <td class="right">
                                <a class="jaune" href="accueilEnseignant/modifierModule.php?module=<?php echo $LecModu["idModule"]; ?>">Modifier</a>
                                <a class="orange" href="accueilEnseignant/supprimerModule.php?module=<?php echo $LecModu["idModule"]; ?>">Supprimer</a>
                            </td>
                        </tr>
                    </table>
                    <table class="large">
                        <tr>
                            <?php
                            // Affichage du nom et prénom du responsable
                            echo"<td class='projet'>Responsable :</td><td>".$prenom." ".$nom."</td>";
                            ?>
                        </tr>
                        <tr>
                            <?php
                            // Affichage des années correspondants aux modules
                            echo"<td>Année(s) :</td><td>".$LecModu['anneeModule']."</td>";
                            ?>
                        </tr>
                        <tr class='projet'>
                            <?php
                            // Récupération des projets du module
                            $ReqPro = "SELECT idProjet, nomProjet FROM PROJET WHERE idModulePere='".$LecModu["idModule"]."'";
                            $TabPro = mysqli_query($BDD, $ReqPro);
                            
                            // Affichage des projets du module
                            echo"<td>Projet(s) :</td><td>";
                            
                            while($LecPro = mysqli_fetch_array($TabPro))
                            {
                                echo $LecPro['nomProjet']."<br/>";
                            }
                            echo "</td>";
                            
                            mysqli_free_result($TabPro);
                            ?>
                        </tr>
                    </table>
                    <?php
                }
            }
            
            // S'il n'y a pas de modules
            else 
            {
                echo "<p>Il n’y a aucun module !</p>";
                
            }
            
            mysqli_free_result($TabModu);
            ?>
            </article>
            
            <h2>Vos projets en tant que client/tuteur </h2> 
            
            <article>
            <?php
            // Récupération des projets où l'enseignant est impliqué en tant que tuteur 
            $ReqPro = "SELECT nomProjet, idProjet FROM PROJET, GROUPE, gere, CONNEXION WHERE login='$identifiant' AND login=loginClient AND idGroupeGe=idGroupe AND idProjetGr=idProjet";
            $TabPro = mysqli_query($BDD, $ReqPro);
            
            // S'il y a des projets
            if(mysqli_num_rows($TabPro) != 0)
            {
                // Affichage des projets
                while($LecPro = mysqli_fetch_array($TabPro))
                {
                    // Lien vers les projets
                    echo"<table class='large'><tr><td><a href='accueilEnseignant/projet.php?projet=".$LecPro["idProjet"]."'>".$LecPro["nomProjet"]."</a></td>";
                    
                    // Lien vers la documentation liée au projet
                    echo"<td class='right'><a class='bleu' href='accueilEnseignant/docProjet.php?projet=".$LecPro["idProjet"]."'>Documentation</a></td></tr></table>";
                    
                    // Récupération des informations liées au projet (durée, date, ett taille du groupe
                    $ReqInfo = "SELECT duree, dateLimite,tailleGpMin, tailleGpMax FROM PROJET WHERE idProjet='".$LecPro["idProjet"]."'";
                    $TabInfo = mysqli_query($BDD, $ReqInfo);
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
                    
                    if ($nbGroupes == NULL)
                    {
                        $nbGroupes = "0";
                    }
                    
                    // Affichage des informations
                    echo"<table class='large'><tr><td>Durée : ".$duree."</td><td>Effectif : ".$effectif."</td></tr>";
                    echo"<tr><td>Date de remise : ".$date."</td><td>Nombre de groupe(s) : ".$nbGroupes." groupe(s)</td></tr></table>";
                }
            }
            
            // S'il n'y a pas de projets
            else 
            {
                echo "<p>Il n’y a aucun projet !</p>";

            }
            
            mysqli_free_result($TabPro);
            ?>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>