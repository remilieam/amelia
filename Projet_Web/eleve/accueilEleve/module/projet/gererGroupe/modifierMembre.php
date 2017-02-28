<!DOCTYPE html>

<?php
    require("../../../../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../../../../connexion.php");
    }
    
    // Récupération des identifiants et des noms du groupe, du projet et du module auquel appartient le membre à modifier
    if(isset($_GET["groupe"]) && isset($_GET["membre"]))
    {
        $ReqGrp = "SELECT * FROM APPARTIENT, GROUPE, PROJET, MODULE WHERE anneeModule LIKE '%".$_SESSION["_annee"]."%' AND idModule = idModulePere AND idProjet = idProjetGr AND idGroupe = idGroupeAp AND loginEleveAp = '".$_GET["membre"]."' AND admin <> 2 AND loginEleveAp <> '".$_COOKIE["_idf"]."' AND idGroupeAp = ".$_GET["groupe"];
        $TabGrp = mysqli_query($BDD,$ReqGrp);
        $LecGrp = mysqli_fetch_array($TabGrp);
        mysqli_free_result($TabGrp);
        
        $idGroupe = $LecGrp["idGroupe"];
        $nomGroupe = $LecGrp["nomGroupe"];
        $idProjet = $LecGrp["idProjet"];
        $nomProjet = $LecGrp["nomProjet"];
        $idModule = $LecGrp["idModule"];
        $nomModule = $LecGrp["nomModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["groupe"]) || !isset($_GET["membre"]) || $LecGrp == NULL)
    {
?>
        <script>
            window.location.href = "../../../../accueilEleve.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../../../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../../../images/amelia.ico" />
        <title>AMÉLIA – Modifier les droits d’un membre</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../../../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../../../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr class="header">
                <td class="header"><a href="../../../../accueilEleve.php">Accueil</a> > <a href="../../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>"><?php echo $nomGroupe; ?></a> > Modifier les droits d’un membre</td>
                <td class="right"><a href="../gererGroupe.php?groupe=<?php echo $idGroupe; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Modifier les droits d’un membre</h2>
            
            <article>
                <form method="POST">
                    <p>Voulez-vous vraiment modifier les droits du membre ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert" /> <input type="submit" name="_non" value="Non" class="orange" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Cas où l’élève veut vraiment modifier les droits d’un membre
    if(isset($_POST["_oui"]))
    {
        if($_GET["admin"] == 2)
        {
            $ReqPrp = "UPDATE APPARTIENT SET admin = 2 WHERE loginEleveAp = '".$_GET["membre"]."'";
            $ReqPrt = "UPDATE APPARTIENT SET admin = 1 WHERE loginEleveAp = '".$_COOKIE["_idf"]."'";
            
            if(mysqli_query($BDD,$ReqPrp) && mysqli_query($BDD,$ReqPrt))
            {
?>
                <script>
                    alert("Votre requête a bien été exécutée !");
                    window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre requête n’a pas pu être exécutée.\nVeuillez réessayer.");
                    window.location.href = "modifierMembre.php?admin=2&membre=<?php echo $_GET["membre"]; ?>&groupe=<?php echo $idGroupe; ?>";
                </script>
<?php
            }
        }
        
        if($_GET["admin"] == 1)
        {
            // Recherche du statut actuel du membre
            $ReqChx = "SELECT * FROM APPARTIENT WHERE idGroupeAp = ".$idGroupe." AND loginEleveAp = '".$_GET["membre"]."'";
            $TabChx = mysqli_query($BDD,$ReqChx);
            $LecChx = mysqli_fetch_array($TabChx);
            mysqli_free_result($TabChx);
            
            // S’il n’était pas administrateur, il le devient
            if($LecChx["admin"] == 0)
            {
                $ReqAdm = "UPDATE APPARTIENT SET admin = 1 WHERE idGroupeAp = ".$idGroupe." AND loginEleveAp = '".$_GET["membre"]."'";
                
                if(mysqli_query($BDD,$ReqAdm))
                {
?>
                    <script>
                        alert("Votre requête a bien été exécutée !");
                        window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
                    </script>
<?php
                }
                
                else 
                {
?>
                    <script>
                        alert("Erreur ! Votre requête n’a pas pu être exécutée.\nVeuillez réessayer.");
                        window.location.href = "modifierMembre.php?admin=1&membre=<?php echo $_GET["membre"]; ?>&groupe=<?php echo $idGroupe; ?>";
                    </script>
<?php
                }
            }
            
            // S’il était administrateur, il perd ses droits
            else 
            {
                $ReqAdm = "UPDATE APPARTIENT SET admin = 0 WHERE idGroupeAp = ".$idGroupe." AND loginEleveAp = '".$_GET["membre"]."'";
                
                if(mysqli_query($BDD,$ReqAdm))
                {
?>
                    <script>
                        alert("Votre requête a bien été exécutée !");
                        window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
                    </script>
<?php
                }
                
                else 
                {
?>
                    <script>
                        alert("Erreur ! Votre requête n’a pas pu être exécutée.\nVeuillez réessayer.");
                        window.location.href = "modifierMembre.php?admin=1&membre=<?php echo $_GET["membre"]; ?>&groupe=<?php echo $idGroupe; ?>";
                    </script>
<?php
                }
            }
        }
    }
    
    // Cas où l’élève se ravise
    elseif(isset($_POST["_non"]))
    {
?>
            <script>
                window.location.href = "../gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
            </script>
<?php
    }
    
    mysqli_close($BDD);
?>