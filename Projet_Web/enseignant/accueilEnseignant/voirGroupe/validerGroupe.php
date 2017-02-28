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
        $ReqGrp = "SELECT * FROM MODULE, PROJET, GROUPE WHERE idModule = idModulePere AND idProjet = idProjetGr AND loginEnseiResp = '".$_COOKIE["_idf"]."' AND idGroupe = ".$_GET["groupe"];
        $TabGrp = mysqli_query($BDD,$ReqGrp);
        $LecGrp = mysqli_fetch_array($TabGrp);
        mysqli_free_result($TabGrp);
        
        $nomGroupe = $LecGrp["nomGroupe"];
        $idGroupe = $LecGrp["idGroupe"];
        $nomProjet = $LecGrp["nomProjet"];
        $idProjet = $LecGrp["idProjet"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["groupe"]) || $LecGrp == NULL)
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
            AMÉLIA – Validation du groupe <?php echo $nomGroupe; ?>
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
                <td><a href="../../accueilEnseignant.php">Accueil</a> > <a href="../voirGroupe.php?projet=<?php echo $idProjet; ?>">Actualités du projet <?php echo $nomProjet; ?></a> > Validation du groupe <?php echo $nomGroupe; ?></td>
                <td class="right"><a href="../voirGroupe.php?projet=<?php echo $idProjet; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Validation du groupe <?php echo $nomGroupe ?></h2> 
            
            <article>
                <form method="POST">
                    <p>Cochez les personnes qui peuvent accéder au groupe :</p>
                <?php
                    // Récupération de l’ensemble des clients et des enseignants
                    $ReqVis = "SELECT * FROM CONNEXION WHERE login <> '".$_COOKIE["_idf"]."' AND (statut = 'enseignant' OR statut = 'client')";
                    $TabVis = mysqli_query($BDD,$ReqVis);
                    
                    $i = 0;
                    
                    while($LecVis = mysqli_fetch_array($TabVis))
                    {
                ?>
                    <p><input type="checkbox" name="_acces_<?php echo $i; ?>" value="<?php echo $LecVis["login"]; ?>"/> <?php echo $LecVis["prenom"]." ".$LecVis["nom"]; ?></p>
                <?php
                        $i += 1;
                    }
                    
                    mysqli_free_result($TabVis);
                ?>
                    <p>Êtes-vous sûr de vouloir valider le groupe <?php echo $nomGroupe ?> ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert" /> <input type="submit" name="_non" value="Non" class="orange" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    // Si l'enseignant accepte (appuie sur "Oui")
    if(isset($_POST["_oui"])) 
    {
        $Nb = 0;
        
        // Récupération des personnes cochées dans un tableau
        for($j = 0; $j < $i; $j++)
        {
            if(isset($_POST["_acces_$j"]))
            {
                $Acces[$Nb] = $_POST["_acces_$j"];
                $Nb += 1;
            }
        }
        
        for($k = 0; $k < $Nb; $k++)
        {
            $ReqAcc = "INSERT INTO GERE (loginClient,idGroupeGe) VALUES ('".$Acces[$k]."',".$idGroupe.")";
            
            // En cas d’échec de l’ajout d’une des personnes, on annule toutes les actions précédentes
            if(!mysqli_query($BDD,$ReqAcc)) 
            {
                $ReqSpr = "DELETE FROM GERE WHERE idGroupeGe = ".$idGroupe;
?>
                <script>
                    alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                    window.location.href = 'accepterGroupe.php?groupe=<?php echo $idGroupe; ?>';
                </script>
<?php
            }
        }
        
        // Modification du statut du groupe : groupe validé, soit '2'
        $ReqVal="UPDATE GROUPE SET validation=2 WHERE idGroupe=".$_GET["groupe"]."";
        
        if(mysqli_query($BDD, $ReqVal))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers "voir groupe"
            ?>
            <script> alert("<?php echo htmlspecialchars('Le groupe a bien été validé !', ENT_QUOTES); ?>");
            window.location.href='../voirGroupe.php?projet=<?php echo $idProjet; ?>';</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
            <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
            window.location.href='accepterGroupe.php?groupe=<?php echo $idGroupe; ?>';</script>
            <?php
        }
    }
    
    // Si l'enseignant refuse (appuie sur "Non")
    elseif(isset($_POST["_non"]))
    {
        // Redirection vers "voir groupe"
        header( "location: ../voirGroupe.php?projet=$idProjet");
    }
?>