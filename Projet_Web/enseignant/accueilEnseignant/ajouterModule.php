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
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="../../stylesheet.css" />
        <link rel="shortcut icon" href="../../images/amelia.ico" />
        <title>
            AMÉLIA – Ajouter un module
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
                <td><a href="../accueilEnseignant.php">Accueil</a> > Ajouter un module</td>
                <td class="right"><a href="../accueilEnseignant.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Ajouter un module</h2>
            
            <article>
                <form method="POST">
                    <p>
                        Nom du module :
                        <input type="text" name="_nomModule" id="_nomModule" required />
                    </p>
                    <p>Pour quelle(s) année(s) est destiné le module ?</p>
                    <input type="checkbox" name="_annee1" id="1A" value="1A" checked />
                    <label for= "1A"> 1A </label> <br/>
                    <input type="checkbox" name="_annee2" id="2A" value="2A" />
                    <label for= "2A"> 2A </label> <br/>
                    <input type="checkbox" name="_annee3" id="3A" value="3A" />
                    <label for= "3A"> 3A </label>
                    <p class="right"><input type="submit" name="_ajouter" value="Ajouter" class="vert"/></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_ajouter"])) 
    {
        // Mise en majuscules du nom du module
        $NOM_MODULE = strtoupper($_POST['_nomModule']);
        $NOM_MODULE = str_replace("'","’",$NOM_MODULE);
        
        // Chaîne pour les années
        if(isset($_POST['_annee1'])) { $A = "1A"; } else { $A = ""; }
        if(isset($_POST['_annee2'])) { $B = "2A"; } else { $B = ""; }
        if(isset($_POST['_annee3'])) { $C = "3A"; } else { $C = ""; }
        
        // Mise en forme de l'année
        $annee = $A.$B.$C;
        
        // Booléen permettant de vérifier si le module entré porte le même nom qu'un module existant et une même année
        $moduleDejaExistant = false;
        
        // Vérification si le module n'existe pas déjà
        $ReqVerif = "SELECT * FROM MODULE WHERE nomModule = '$NOM_MODULE' AND anneeModule = '$annee'";
        $TabVerif = mysqli_query($BDD, $ReqVerif);
        $LecVerif = mysqli_fetch_array($TabVerif);
        mysqli_free_result($TabVerif);
        
        if($LecVerif != NULL)
        {
            $moduleDejaExistant = true;
        }
        
        // Insertion du module dans la base de données
        $ReqAjout = "INSERT INTO MODULE (nomModule,anneeModule,loginEnseiResp) VALUES ('$NOM_MODULE','$annee','".$_COOKIE["_idf"]."')";
        
        // Si le module n'exsite pas déjà, et que tous les champs sont remplis et que la requête a bien été exécutée
        if(!$moduleDejaExistant && $annee != "" && mysqli_query($BDD, $ReqAjout))
        {
            ?>
            <script> alert("<?php echo htmlspecialchars('Le module a bien été créé !', ENT_QUOTES); ?>");
            window.location.href="../accueilEnseignant.php";</script>
            <?php
        }
        
        // Si le module existe déjà, ou que tous les champs ne sont pas remplis ou que la requête n'a pas été exécutée
        else
        {
            // Message d'alerte
            ?>
                <script> alert("<?php echo htmlspecialchars('Erreur !', ENT_QUOTES); ?>");
                window.location.href="ajouterModule.php";</script>
            <?php
        }
    }
?>