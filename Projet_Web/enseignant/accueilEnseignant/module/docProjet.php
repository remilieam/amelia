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
    if(isset($_GET["projet"]))
    {
        $ReqPrj = "SELECT * FROM PROJET, MODULE WHERE idModule = idModulePere AND loginEnseiResp = '".$_COOKIE["_idf"]."' AND idProjet = ".$_GET["projet"];
        $TabPrj = mysqli_query($BDD,$ReqPrj);
        $LecPrj = mysqli_fetch_array($TabPrj);
        mysqli_free_result($TabPrj);
        
        $nomProjet = $LecPrj["nomProjet"];
        $idProjet = $LecPrj["idProjet"];
        $nomModule = $LecPrj["nomModule"];
        $idModule = $LecPrj["idModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["projet"]) || $LecPrj == NULL)
    {
?>
        <script>
            window.location.href = "../../accueilEnseignant.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../images/amelia.ico" />
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
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <!--Fil d'ariane-->
        <table class="header">
            <tr>
                <td><a href="../../accueilEnseignant.php">Accueil</a> > <a href='../module.php?module=<?php echo $idModule; ?>'><?php echo $nomModule; ?></a> > Documents de <?php echo $nomProjet; ?></td>
                <td class="right"><a href='../module.php?module=<?php echo $idModule; ?>' class='blanc'>Retour</a></td>
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
                ?>
                <table class="large">
                <?php
                // Affichage des documents
                while($LecDoc = mysqli_fetch_array($TabDoc))
                {
                    echo "<tr><td><a href='../../../annexe/".$LecDoc['urlAnnexe']."'> ".$LecDoc['nomAnnexe']." </a></td>";
                    // Lien pout supprimer les documents
                    echo "<td class='right'><a class='orange' href='docProjet/supprimerDoc.php?annexe=".$LecDoc['idAnnexe']."'> Supprimer </a></td></tr>";
                }
                ?>
                </table>
                <?php
            }
            
            ?>
            </article>
            
            <h2>Ajouter un document</h2>
            
            <article>
                <form method="POST" enctype="multipart/form-data">
                    <p>
                        Ajouter un document :
                        <input type="file" name="_fichier" />
                    </p>
                    <p>
                        Entrer le nom que vous désirez donner à votre document : 
                        <input type="text" name="_nomDoc" />
                    </p>
                    <p class="right"><input type="submit" name="_ajouter" value="Ajouter" class="vert"/></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php 
    // Si l'utilisateur clique sur Ajouter
    if(isset($_POST["_ajouter"]) && isset($_FILES["_fichier"]) && $_FILES["_fichier"]["error"] == 0)
    {
        $urlFichier = $_FILES["_fichier"]["name"];
        $nomFichier = $_POST["_nomDoc"];
        
        // Si l'utilisaeur ne nomme pas son document
        if($_POST["_nomDoc"] == NULL)
        {
            $nomFichier = $urlFichier;
        }
        
        // Vérification que le document n’existe pas déjà
        $ReqAnn = "SELECT * FROM ANNEXE WHERE urlAnnexe = '".$urlFichier."'";
        $TabAnn = mysqli_query($BDD,$ReqAnn);
        $LecAnn = mysqli_fetch_array($TabAnn);
        mysqli_free_result($TabAnn);
        
        // Cas où un document porte le même nom
        if($LecAnn != NULL)
        {
            $ReqNom = "UPDATE ANNEXE SET urlAnnexe = '".$LecAnn["idAnnexe"]."_".$urlFichier."' WHERE urlAnnexe = '".$urlFichier."'";
            mysqli_query($BDD,$ReqNom);
            rename("../../../annexe/".$urlFichier,"../../../annexe/".$LecAnn["idAnnexe"]."_".$urlFichier);
        }
        
        // Ajout du fichier à la base de données et sur le serveur
        $ReqAjoutDoc = "INSERT INTO ANNEXE (nomAnnexe, urlAnnexe, idProjetAn) VALUES ('".$nomFichier."','".$urlFichier."',$idProjet)";
        if(mysqli_query($BDD,$ReqAjoutDoc) && move_uploaded_file($_FILES["_fichier"]["tmp_name"],"../../../annexe/".basename($urlFichier)))
        {
            // Pop-up affichant que le document a été ajouté, et redirection vers la page de documents
            ?>
            <script> alert("<?php echo htmlspecialchars('Le document a bien été ajouté !', ENT_QUOTES); ?>");
            window.location.href='docProjet.php?projet=<?php echo $idProjet; ?>';</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
            <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
            window.location.href='docProjet.php?projet=<?php echo $idProjet; ?>';</script>
            <?php
        }
    }
?>