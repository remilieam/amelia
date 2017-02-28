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
            AMÉLIA – Refus du groupe <?php echo $nomGroupe; ?>
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
                <td><a href="../../accueilEnseignant.php">Accueil</a> > <a href="../voirGroupe.php?projet=<?php echo $idProjet; ?>">Actualités du projet <?php echo $nomProjet; ?></a> > Refus du groupe <?php echo $nomGroupe; ?></td>
                <td class="right"><a href="../voirGroupe.php?projet=<?php echo $idProjet; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Refus du groupe <?php echo $nomGroupe ?></h2> 
            
            <article>
                <form method="POST">
                    <p>Expliquer brièvement la raison de votre refus :</p>
                    <textarea name="_justif" rows=5></textarea>
                    <p>Êtes-vous sûr de vouloir refuser le groupe <?php echo $nomGroupe ?> ?</p>
                    <p class="right"><input type="submit" name="_oui" value="Oui" class="vert"/> <input type="submit" name="_non" value="Non" class="orange"/></p>
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
        // Gestion des apostrophes
        $justif = $_POST["_justif"];
        $justif = str_replace("'","’",$justif);
        
        // Récupération du login du propriétaire du groupe dans lequel on refuse d’aller
        $ReqLog = "SELECT * FROM APPARTIENT WHERE admin = 2 AND idGroupeAp = ".$_GET["groupe"];
        $TabLog = mysqli_query($BDD,$ReqLog);
        $LecLog = mysqli_fetch_array($TabLog);
        mysqli_free_result($TabLog);
        
        // Envoi du message de justification du refus
        $ReqJstf = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."','".$LecLog["loginEleveAp"]."','Refus de la validation de votre groupe $nomGroupe','".$justif."')";
        
        // Modification du statut du groupe : groupe refusé, soit '0'
        $ReqVal="UPDATE GROUPE SET validation=0 WHERE idGroupe=".$_GET["groupe"];
        
        if(mysqli_query($BDD, $ReqVal) && mysqli_query($BDD,$ReqJstf))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers "voir groupe"
            ?>
            <script> alert("<?php echo htmlspecialchars('Le groupe a bien été refusé !', ENT_QUOTES); ?>");
            window.location.href='../voirGroupe.php?projet=<?php echo $idProjet; ?>';</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
            <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
            window.location.href='refuserGroupe.php?groupe=<?php echo $idGroupe; ?>';</script>
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