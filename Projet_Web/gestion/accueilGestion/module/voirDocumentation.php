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
    
    // Récupération des identifiants et des noms du projet et du module auquel appartient le projet
    if(isset($_GET["projet"]))
    {
        $ReqPrj = "SELECT * FROM PROJET, MODULE WHERE idModule = idModulePere AND idProjet = ".$_GET["projet"];
        $TabPrj = mysqli_query($BDD,$ReqPrj);
        $LecPrj = mysqli_fetch_array($TabPrj);
        mysqli_free_result($TabPrj);
    
        $idProjet = $LecPrj["idProjet"];
        $nomProjet = $LecPrj["nomProjet"];
        $idModule = $LecPrj["idModule"];
        $nomModule = $LecPrj["nomModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["projet"]) || $LecPrj == NULL)
    {
?>
        <script>
            window.location.href = "../../accueilGestion.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../images/amelia.ico" />
        <title>AMÉLIA – <?php echo $nomProjet; ?></title>
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
        
        <table class="header">
            <tr class="header">
                <td class="header"><a href="../../accueilGestion.php">Accueil</a> > <a href="../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <?php echo $nomProjet; ?></td>
                <td class="right"><a href="../module.php?module=<?php echo $idModule; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Documentation du projet</h2>
            
            <article>
<?php
    // Récupération de tous les documents mis à la disposition de l’élève par l’enseignant responsable
    $ReqDoc = "SELECT * FROM ANNEXE WHERE idProjetAn = ".$idProjet;
    $TabDoc = mysqli_query($BDD,$ReqDoc);
    
    // Vérification pour savoir si l’enseignant a posté des documents
    if(mysqli_num_rows($TabDoc) != 0)
    {
?>
                <ul>
<?php
        // Si oui, on les affiche
        while($LecDoc = mysqli_fetch_array($TabDoc))
        {
        
?>
                    <li><a href="../../../annexe/<?php echo $LecDoc["urlAnnexe"]; ?>"><?php echo $LecDoc["nomAnnexe"]; ?></a></li>
<?php
        }
?>
                </ul>
<?php
    }
    
    // Si non, on affiche qu’il n’y aucun document
    else 
    {
?>
                <p>Aucune documentation…</p>
<?php
    }
    
    mysqli_free_result($TabDoc);
?>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php    
    mysqli_close($BDD);
?>
