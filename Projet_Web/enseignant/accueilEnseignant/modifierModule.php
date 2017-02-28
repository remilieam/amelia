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
    if(isset($_GET["module"]))
    {
        $ReqMdl = "SELECT * FROM MODULE WHERE loginEnseiResp = '".$_COOKIE["_idf"]."' AND idModule = ".$_GET["module"];
        $TabMdl = mysqli_query($BDD,$ReqMdl);
        $LecMdl = mysqli_fetch_array($TabMdl);
        mysqli_free_result($TabMdl);
    
        $nomModule = $LecMdl["nomModule"];
        $idModule = $LecMdl["idModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["module"]) || $LecMdl == NULL)
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
            AMÉLIA – <?php echo "Modifier le module ".$nomModule; ?>
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
                <td><a href="../accueilEnseignant.php">Accueil</a> > Modification de <?php echo $nomModule; ?></td>
                <td class="right"><a href="../accueilEnseignant.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2> Modification de <?php echo $nomModule ?></h2>
            
            <article>
                <form method="POST">
                    <p>
                        Modfier le nom du module :
                        <input type="text" name="_nomModuleModi" id="_nomModuleModi" required />
                    </p>
                    <p>Pour quelle année est destinée le module ?</p>
                    <input type="checkbox" name="_annee1" id="1A" value="1A" checked />
                    <label for= "1A"> 1A </label> <br/>
                    <input type="checkbox" name="_annee2" id="2A" value="2A" />
                    <label for= "2A"> 2A </label> <br/>
                    <input type="checkbox" name="_annee3" id="3A" value="3A" />
                    <label for= "3A"> 3A </label>
                    <p class="right"><input type="submit" name="_modifier" value="Modifier" class="jaune" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

            
<?php
    if(isset($_POST["_modifier"])) 
    {
        // Mise en majuscules du nom du module
        $NOM_MODULE_MODI = strtoupper($_POST['_nomModuleModi']);
        $NOM_MODULE_MODI = str_replace("'","’",$NOM_MODULE_MODI);
        
        // Chaîne pour les années
        if(isset($_POST['_annee1'])) { $A = "1A"; } else { $A = ""; }
        if(isset($_POST['_annee2'])) { $B = "2A"; } else { $B = ""; }
        if(isset($_POST['_annee3'])) { $C = "3A"; } else { $C = ""; }
        
        // Mise en forme de l'année
        $annee = $A.$B.$C;
        
        // Requêtes pour la modification du nom du module et de l'année du module
        $RqtModi = "UPDATE MODULE SET nomModule = '".$NOM_MODULE_MODI."', anneeModule = '".$annee."' WHERE idModule='".$idModule."'";
        
        // Pop-up affichant que la requête a été effectuée, et redirection vers la page d'accueil
        if(mysqli_query($BDD, $RqtModi))
        {
            ?>
            <script>alert("<?php echo htmlspecialchars('Le module a bien été modifié !', ENT_QUOTES); ?>");
            window.location.href="../accueilEnseignant.php";</script>
            <?php
        }
        
        else 
        {
            // Message d'alerte
            ?>
                <script>alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                window.location.href="modifierModule.php?module=<?php echo $idModule; ?>";</script>
            <?php
        }
    }
?>