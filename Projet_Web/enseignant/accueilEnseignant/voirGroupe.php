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
        $ReqPrj = "SELECT * FROM MODULE, PROJET WHERE idModule = idModulePere AND loginEnseiResp = '".$_COOKIE["_idf"]."' AND idProjet = ".$_GET["projet"];
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
            AMÉLIA – Actualités du projet <?php echo $nomProjet; ?>
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
                <td><a href="../accueilEnseignant.php">Accueil</a> > Actualités du projet <?php echo $nomProjet; ?></td>
                <td class="right"><a href="../accueilEnseignant.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
        <?php
        // Récupération des groupes non validés
        $ReqGr = "SELECT nomGroupe, idGroupe, description FROM GROUPE WHERE idProjetGr=".$_GET["projet"]." AND validation=1";
        $TabGr = mysqli_query($BDD, $ReqGr);
        
        if(mysqli_num_rows($TabGr) != 0)
        {
            while($LecGr =  mysqli_fetch_array($TabGr))
            {
                // Affichage du groupe
                echo "<h2>".$LecGr["nomGroupe"]."</h2>";
                ?>
                <article>
                <?php
                // Récupération des élèves liés au groupe
                $ReqEle = "SELECT nom, prenom FROM CONNEXION, GROUPE, appartient WHERE loginEleveAp=login AND idGroupe=idGroupeAp AND idGroupe=".$LecGr["idGroupe"]." AND validation=1";
                $TabEle = mysqli_query($BDD, $ReqEle);
                
                // Affichage des élèves
                echo "<table class='large'><tr class='projet'><td class='projet'>Élèves :</td><td>";
                
                while($LecEle = mysqli_fetch_array($TabEle))     
                {
                    echo $LecEle['nom']." ".$LecEle['prenom']."<br/>";
                }
                
                echo "</td></tr>";
                
                // Affichage de la description du groupe       
                if($LecGr["description"] != NULL || $LecGr["description"] != "")
                {
                    $description = str_replace(' "',' “',$LecGr["description"]);
                    $description = str_replace('"','”',$description);
                    echo "<tr class='projet'><td>Description :</td><td>$description</td></tr>";
                }
                
                echo "</td></tr></table>";
                
                // Liens vers la validation/refus de groupe
                echo"<p class='right'><a class='vert' href='voirGroupe/validerGroupe.php?groupe=".$LecGr["idGroupe"]."'>Valider</a> ";
                echo"<a class='orange' href='voirGroupe/refuserGroupe.php?groupe=".$LecGr["idGroupe"]."'>Refuser</a></p>";  
            ?>
                </article>
            <?php    
            }
        }
        
        else 
        {
            header("location: ../accueilEnseignant.php");
        }
        ?>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>