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
        $ReqPrj = "SELECT * FROM GERE, GROUPE, PROJET WHERE idGroupeGe = idGroupe AND idProjetGr = idProjet AND loginClient = '".$_COOKIE["_idf"]."' AND idProjet = ".$_GET["projet"];
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
            AMÉLIA – <?php echo $nomProjet; ?>
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
                <td><a href="../accueilEnseignant.php">Accueil</a> > <?php echo $nomProjet; ?></td>
                <td class="right"><a href="../accueilEnseignant.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
        <?php
        // Récupération des groupes du projet
        $ReqGr = "SELECT nomGroupe, idGroupe, description FROM GROUPE WHERE idProjetGr='$idProjet' AND validation=2";
        $TabGr =  mysqli_query($BDD, $ReqGr);
        
        // S'il y a des groupes validés
        if(mysqli_num_rows($TabGr) != 0)
        {
            // Affichage des groupes du projet
            while($LecGr = mysqli_fetch_array($TabGr))
            {
                echo"<h2><a href='projet/groupe.php?groupe=".$LecGr["idGroupe"]."'>".$LecGr["nomGroupe"]."</a></h2>";
                ?>
                
                <article>
                <?php
                // Récupération des élèves liés au groupe
                $ReqEleve="SELECT nom, prenom FROM CONNEXION, GROUPE, appartient WHERE loginEleveAp=login AND idGroupe=idGroupeAp AND idGroupe=".$LecGr["idGroupe"]."";
                $TabEleve=  mysqli_query($BDD, $ReqEleve);
                
                // Affichage des informations liées aux élèves
                echo"<table class='large'><tr class='projet'><td class='projet'>Elèves :</td><td>";
                
                while($LecEleve = mysqli_fetch_array($TabEleve))     
                {
                    echo $LecEleve['prenom']." ".$LecEleve['nom']."<br/>";
                }
                
                // Affichage de la descriptionrption du groupe s'il y en a une       
                if($LecGr["description"] != NULL || $LecGr["description"] != "")
                {
                    $description = str_replace(' "',' “',$LecGr["description"]);
                    $description = str_replace('"','”',$description);
                    echo "</td></tr><tr class='projet'><td>Description :</td><td>$description</td></tr></table>";
                }
                ?>
                </article>
                <?php
            }
        }
        
        // S'il n'y a pas de groupe
        else 
        {
            echo "<h2>$nomProjet</h2>";
            echo "<article><p>Le groupe n’a pas encore été validé par l’enseignant responsable.</p></article>";
        }

        ?>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    <head>
        
    </head>
</html>

