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
        <meta charset="utf-8" />
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
<?php
    // Récupération des groupe du projet
    $ReqGrp = "SELECT * FROM GROUPE WHERE idProjetGr = ".$idProjet." ORDER BY nomGroupe";
    $TabGrp = mysqli_query($BDD,$ReqGrp);
    
    // Vérification pour savoir si l’élève appartiant à un groupe
    if(mysqli_num_rows($TabGrp) != 0)
    {
        // Si oui, on l’affiche
        while($LecGrp = mysqli_fetch_array($TabGrp))
        {
            // Récupération des noms et prénoms des élèves du groupe
            $ReqElv = "SELECT * FROM CONNEXION, APPARTIENT, GROUPE WHERE login = loginEleveAp AND idGroupeAp = idGroupe AND idGroupe = ".$LecGrp["idGroupe"];
            $TabElv = mysqli_query($BDD,$ReqElv);
            
            // Récupération des noms et des liens permettant d’accéder aux documents remis
            $ReqDoc = "SELECT nomRendu, urlRendu FROM RENDU, GROUPE WHERE idGroupeRe = idGroupe AND idGroupe = ".$LecGrp["idGroupe"];
            $TabDoc = mysqli_query($BDD,$ReqDoc);
            
            // Mise en forme de la description
            $Dcr = $LecGrp["description"];
            $Dcr = str_replace(' "',' “',$Dcr);
            $Dcr = str_replace('"','”',$Dcr);
?>
            
            <table class="large">
                <tr>
                    <td><h2><?php echo $LecGrp["nomGroupe"]; ?></h2></td>
                    <td class="right"><a href="projet/supprimerGroupe.php?groupe=<?php echo $LecGrp["idGroupe"]; ?>" class="orange">Supprimer le projet</a></td>
                </tr>
            </table>
            
            <article>
                <table class="large">
<?php
            // Affichage éventuel de la description du groupe
            if($LecGrp["description"] != NULL && $LecGrp["description"] != "")
            {
?>
                    <tr class="projet">
                        <td>Description :</td>
                        <td><?php echo $Dcr; ?></td>
                    </tr>
<?php
            }
?>
                    <tr class="projet">
                        <td class="projet">Élèves :</td>
                        <td><?php
            while($LecElv = mysqli_fetch_array($TabElv))
            {
                if($LecElv["admin"] == 1)
                {
                    $Statut = " (Administrateur)";
                }
                
                elseif($LecElv["admin"] == 2)
                {
                    $Statut = " (Propriétaire)";
                }
                
                else 
                {
                    $Statut = "";
                }
                
                echo $LecElv["prenom"]." ".$LecElv["nom"].$Statut; ?><br/><?php
            } ?></td>
                    </tr>
                    <tr class="projet">
                        <td>Documents remis :</td>
                        <td><?php
            if(mysqli_num_rows($TabDoc) != 0)
            {
                while($LecDoc = mysqli_fetch_array($TabDoc))
                { ?><a href="../../../rendu/<?php echo $LecDoc["urlRendu"]?>"><?php echo $LecDoc["nomRendu"]; ?></a><br/><?php }
            }
            
            else { ?>Aucun document remis…<?php } ?></td>
                    </tr>
                </table>
            </article>
            
<?php
            mysqli_free_result($TabDoc);
            mysqli_free_result($TabElv);
        }
    }
    
    else 
    {
?>
            
            <h2><?php echo $nomProjet; ?></a></h2>
            
            <article>
                <p>Aucun groupe dans ce projet…</p>
            </article>
<?php
    }
    
    mysqli_free_result($TabGrp);
?>
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php    
    mysqli_close($BDD);
?>