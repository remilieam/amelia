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
    if(isset($_GET["projet"]))
    {
        $ReqPrj = "SELECT * FROM GERE, GROUPE, PROJET WHERE idProjet = idProjetGr AND idGroupe = idGroupeGe AND loginClient = '".$_COOKIE["_idf"]."' AND idProjet = ".$_GET["projet"];
        $TabPrj = mysqli_query($BDD,$ReqPrj);
        $LecPrj = mysqli_fetch_array($TabPrj);
        mysqli_free_result($TabPrj);
        
        $nomProjet = $LecPrj["nomProjet"];
        $idProjet = $LecPrj["idProjet"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["projet"]) || $LecPrj == NULL)
    {
?>
        <script>
            window.location.href = "../accueilClient.php";
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
            AMÉLIA – Documents de <?php echo $nomProjet; ?>
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
                <td><a href="../accueilClient.php">Accueil</a> > Documents de <?php echo $nomProjet; ?></td>
                <td class="right"><a href="../accueilClient.php" class='blanc'>Retour</a></td>
            </tr>
        </table>
        
        <section>
        
            <h2>Les documents liés au projet</h2>
            
            <?php

            // Récupération des annexes liées au projet
            $ReqDoc = "SELECT * FROM ANNEXE WHERE idProjetAn=$idProjet"; 
            $TabDoc = mysqli_query($BDD, $ReqDoc);

            ?>
            <article>
            <?php
            // Si il n'y a aucun document
            if(mysqli_num_rows($TabDoc) == 0)
            {
                echo "<p>Aucun document !</p>";
            }

            // Si il y a des documents
            else
            {
                // Affichage des documents
                while($LecDoc = mysqli_fetch_array($TabDoc))
                {
                    echo "<p><a href='../../annexe/".$LecDoc['urlAnnexe']."'> ".$LecDoc['nomAnnexe']."</a></p>";
                }
            }
            
            ?>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>