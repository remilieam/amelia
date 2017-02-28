<!DOCTYPE html>

<?php 
    require("../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
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
            
            <h2>Projets</h2> 
            
            <article>
            
            <?php
            // Récupération des projets du client
            $ReqPro = "SELECT nomProjet, idProjet FROM PROJET, GROUPE, gere WHERE loginClient='".$_COOKIE["_idf"]."' AND idGroupeGe=idGroupe AND idProjetGr=idProjet";
            $TabPro = mysqli_query($BDD, $ReqPro);
            
            // S'il y a des projets
            if(mysqli_num_rows($TabPro) != 0)
            {
                // Affichage des projets
                while($LecPro = mysqli_fetch_array($TabPro))
                {
                    // Lien vers les projets
                    echo"<table class='large'><tr><td><a href='accueilClient/projet.php?projet=".$LecPro["idProjet"]."'>".$LecPro["nomProjet"]."</a></td>";
                    
                    // Lien vers la documentation liée au projet
                    echo"<td class='right'><a class='bleu' href='accueilClient/docProjet.php?projet=".$LecPro["idProjet"]."'>Documentation</a></td></tr></table>";
                    
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