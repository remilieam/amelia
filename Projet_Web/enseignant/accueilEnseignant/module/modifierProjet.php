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
            AMÉLIA – Modifier le projet <?php echo $nomProjet; ?>
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
                <td><a href="../../accueilEnseignant.php">Accueil</a> > <a href='../module.php?module=<?php echo $idModule; ?>'><?php echo $nomModule; ?></a> > Modification de <?php echo $nomProjet; ?></td>
                <td class="right"><a href='../module.php?module=<?php echo $idModule; ?>' class='blanc'>Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Modification du nom du projet</h2>
            
            <article>
                <form method="POST">
                    <p>
                        Nom du projet :
                        <input type="text" name="_nomProjetMo" id="_nomProjetMo"/>
                    </p>
                    <p class="right"><input type="submit" name="_modifier1" value="Modifier" class="jaune" /></p>
                </form>           
            </article>
            
            <h2>Modification de la durée du projet</h2>
            
            <article>
                <form method="POST">
                    <p>
                        Durée :
                        <input type="number" min="0" max="1000" name="_dureeMo" id="_dureeMo"/> semaines
                    </p>
                    <p class="right"><input type="submit" name="_modifier2" value="Modifier" class="jaune" /></p>
                </form>
            </article>
            
            <h2>Modification de la date de remise projet</h2>
            
            <article>
                <form method="POST">
                    <p>
                        Date de remise : (aaaa-mm-jj)
                        <input type="date" name="_dateMo" id="_dateMo" value="<?php echo date("Y-m-d" ); ?>"/>
                    </p>
                    <p class="right"><input type="submit" name="_modifier3" value="Modifier" class="jaune" /></p>
                </form>
            </article>
            
            <h2>Modification des effectifs des groupes du projet</h2>
            
            <article>
                <form method="POST">
                    <p>
                        Effectif des groupes :
                        <input type="number" min="0" max="1000" name="_effMinMo" id="_effMinMo"/> - <input type="number" min="0" max="1000" name="_effMaxMo" id="_effMaxMo"/>
                    </p>
                    <p class="right"><input type="submit" name="_modifier4" value="Modifier" class="jaune" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

       
<?php
    if(isset($_POST["_modifier1"])) 
    {
        // Modification du nom du projet
        $RqtModiNom="UPDATE PROJET SET nomProjet = '".str_replace("'","’",$_POST['_nomProjetMo'])."' WHERE idProjet=".$idProjet; 
        
        if(mysqli_query($BDD, $RqtModiNom))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers la page du module
            ?>
                <script> alert("<?php echo htmlspecialchars('Le nom a bien été modifié !', ENT_QUOTES); ?>");
                window.location.href="../module.php?module=<?php echo $idModule; ?>";</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
                <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                window.location.href="modifierProjet.php?projet=<?php echo $idProjet; ?>";</script>
            <?php
        }
    }
    
    else if(isset($_POST["_modifier2"])) 
    {
        // Modification de la durée du projet
        $RqtModiDuree="UPDATE PROJET SET duree = ". $_POST['_dureeMo']." WHERE idProjet=".$idProjet; 
        
        if(mysqli_query($BDD, $RqtModiDuree))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers la page du module
            ?>
                <script> alert("<?php echo htmlspecialchars('Le nom a bien été modifié !', ENT_QUOTES); ?>");
                window.location.href="../module.php?module=<?php echo $idModule; ?>";</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
                <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                window.location.href="modifierProjet.php?projet=<?php echo $idProjet; ?>";</script>
            <?php
        }
    }
    
    else if(isset($_POST["_modifier3"])) 
    {
        // Modification de la date limite
        $RqtModiDate="UPDATE PROJET SET dateLimite = '". $_POST['_dateMo']."' WHERE idProjet=".$idProjet; 
        
        if(mysqli_query($BDD, $RqtModiDate))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers la page du module
            ?>
                <script> alert("<?php echo htmlspecialchars('Le nom a bien été modifié !', ENT_QUOTES); ?>");
                window.location.href="../module.php?module=<?php echo $idModule; ?>";</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
                <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                window.location.href="modifierProjet.php?projet=<?php echo $idProjet; ?>";</script>
            <?php
        }
    }
    
    else if(isset($_POST["_modifier4"])) 
    {
        $Min = $_POST['_effMinMo'];
        $Max = $_POST['_effMaxMo'];
        
        if($Min > $Max)
        {
            $temp = $Min;
            $Min = $Max;
            $Max = $temp;
        }
        
        // Modification du nombre d'élèves par groupe
        $RqtModiEff="UPDATE PROJET SET tailleGpMax = $Max, tailleGpMin = $Min WHERE idProjet=".$idProjet;
        
        if(mysqli_query($BDD, $RqtModiEff))
        {
            // Pop-up affichant que la requête a été effectuée, et redirection vers la page du module
            ?>
                <script> alert("<?php echo htmlspecialchars('Le nom a bien été modifié !', ENT_QUOTES); ?>");
                window.location.href="../module.php?module=<?php echo $idModule; ?>";</script>
            <?php
        }
        
        else 
        {
            // Message d'erreur
            ?>
                <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                window.location.href="modifierProjet.php?projet=<?php echo $idProjet; ?>";</script>
            <?php
        }
    }
?>