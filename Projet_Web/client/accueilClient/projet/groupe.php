<!DOCTYPE html>

<?php
    require("../../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../../connexion.php");
    }
    
    // Récupération de l’identifiant et du nom du module
    if(isset($_GET["groupe"]))
    {
        $ReqGrp = "SELECT * FROM GERE, GROUPE, PROJET WHERE idProjet = idProjetGr AND idGroupe = idGroupeGe AND loginClient = '".$_COOKIE["_idf"]."' AND idGroupe = ".$_GET["groupe"];
        $TabGrp = mysqli_query($BDD,$ReqGrp);
        $LecGrp = mysqli_fetch_array($TabGrp);
        mysqli_free_result($TabGrp);
        
        $nomGroupe = $LecGrp["nomGroupe"];
        $idGroupe = $LecGrp["idGroupe"];
        $description = str_replace(' "',' “',$LecGrp["description"]);
        $description = str_replace('"','”',$description);
        $nomProjet = $LecGrp["nomProjet"];
        $idProjet = $LecGrp["idProjet"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["groupe"]) || $LecGrp == NULL)
    {
?>
        <script>
            window.location.href = "../../../accueilEnseignant.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../../images/amelia.ico" />
        <title>
            AMÉLIA – <?php echo $nomGroupe; ?>
        </title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <!--Fil d'ariane-->
        <table class="header">
            <tr>
                <td><a href="../../accueilClient.php">Accueil</a> > <a href='../projet.php?projet=<?php echo $idProjet; ?>'><?php echo $nomProjet; ?></a> > <?php echo $nomGroupe; ?></td>
                <td class="right"><a href='../projet.php?projet=<?php echo $idProjet; ?>' class='blanc'>Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2><?php echo $nomGroupe; ?></h2>
            
            <article>
            <?php
            // Récupération des informations liées au groupe (élèves, description)
            $ReqEle = "SELECT nom, prenom, description FROM CONNEXION, GROUPE, appartient WHERE loginEleveAp=login AND idGroupe=idGroupeAp AND idGroupe=".$_GET["groupe"]." AND validation=2";
            $TabEle = mysqli_query($BDD, $ReqEle);
            
            // Affichage des élèves
            echo "<table class='large'><tr class='projet'><td class='projet'>Élèves :</td><td>";
            
            while($LecEle = mysqli_fetch_array($TabEle))     
            {
                echo $LecEle['nom']." ".$LecEle['prenom']."<br/>";
            }
            
            echo "</td></tr>";
            
            // Affichage de la description du groupe       
            if($description != NULL || $description = "")
            {
                 echo "<tr class='projet'><td>Description :</td><td>$description</td></tr>";
            }
            
            // Récupération des documents rendus par les élèves
            $ReqDoc = "SELECT nomRendu,urlRendu FROM RENDU WHERE idGroupeRe='".$_GET['groupe']."'";
            $TabDoc = mysqli_query($BDD, $ReqDoc);
            
            // Affichage des documents rendus par les élèves
            echo "<tr class='projet'><td>Documents remis :</td><td>";
            
            // Si il n'y a aucun document
            if(mysqli_num_rows($TabDoc) == 0)
            {
                echo "Aucun document !";
            }
            
            // Si il y a des documents
            else 
            {
                while($LecDoc = mysqli_fetch_array($TabDoc))
                {
                   echo "<a href='../../../rendu/".$LecDoc['urlRendu']."'> ".$LecDoc['nomRendu']."</a><br/>";
                }
            }
            
            echo "</td></tr></table>";
            ?>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>