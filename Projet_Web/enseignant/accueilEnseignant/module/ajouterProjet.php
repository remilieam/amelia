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
    if(isset($_GET["module"]))
    {
        $ReqMdl = "SELECT * FROM MODULE WHERE loginEnseiResp = '".$_COOKIE["_idf"]."' AND idModule = ".$_GET["module"];
        $TabMdl = mysqli_query($BDD,$ReqMdl);
        $LecMdl = mysqli_fetch_array($TabMdl);
        mysqli_free_result($TabMdl);
        
        $nomModule = $LecMdl["nomModule"];
        $idModule = $LecMdl["idModule"];
        $anneeModule = $LecMdl["anneeModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["module"]) || $LecMdl == NULL)
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
            AMÉLIA – Ajouter un projet
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
                <td><a href="../../accueilEnseignant.php">Accueil</a> > <a href='../module.php?module=<?php echo $idModule; ?>'><?php echo $nomModule; ?></a> > Ajouter un projet</td>
                <td class="right"><a href='../module.php?module=<?php echo $idModule; ?>' class='blanc'>Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Ajouter un projet</h2>
            
            <article>
                <form method="POST" enctype="multipart/form-data">
                    <p>
                        Nom du projet :
                        <input type="text" name="_nomProjet" id="_nomProjet" required />
                    </p>
                    <p>
                        Durée :
                        <input type="number" min="0" max="1000" name="_duree" id="_duree" /> semaines
                    </p>
                    <p>
                        Date de remise : (aaaa-mm-jj)
                        <input type="date" name="_date" id="_date" value="<?php echo date("Y-m-d" ); ?>" />
                    </p>
                    <p>
                        Effectif des groupes :
                        <input type="number" min="0" max="1000" name="_effMin" id="_effMin" /> - <input type="number" min="0" max="1000" name="_effMax" id="_effMax" />
                    </p>
                    <p>
                        Faut-il que l'élève postule avec une candidature ?
                        <input type="radio" name="_candi" id="_candi" value="1" />
                        <label for= "1"> Oui </label>
                        <input type="radio" name="_candi" id="_candi" value="0" checked />
                        <label for= "0"> Non </label>
                    </p>
                    <!-- Si un module est destiné à plusieurs promotions, il faut que l'enseignant choississe la/les promotion(s) qui peuvent créer un groupe -->
                    <!-- S'il n'y a qu'une seule promotion dans le module, remplissage automatique dans la base de données de la promotion -->
                    <?php
                    
                    if(strlen($anneeModule) > 2)
                    {
                       ?>
                       <p>Cocher la/les promotion(s) qui peuvent créer des groupes dans le projet :</p>
                       <?php
                       if(substr($anneeModule,0,2) == "1A" )
                       {
                       ?>
                           <input type="checkbox" name="_annee1" value="1A" /> 1A <br/>
                       <?php
                       }
                       if(substr($anneeModule,0,2) == "2A" || substr($anneeModule,2,2) == "2A" )
                       {
                       ?>
                           <input type="checkbox" name="_annee2" value="2A" /> 2A <br/>
                       <?php
                       }
                       if(substr($anneeModule,0,2) == "3A" || substr($anneeModule,2,2) == "3A" || substr($anneeModule,4,2) == "3A")
                       {
                       ?>
                           <input type="checkbox" name="_annee3" value="3A" /> 3A <br/>
                       <?php
                       }
                    }
                    
                    ?>
                    <p>
                        Ajouter un document :
                        <input type="file" name="_fichier" />
                    </p>
                    <p>
                        Entrer le nom que vous désirez donner à votre document :
                        <input type="text" name="_nomDoc" />
                    </p>
                    <p class="right"><input type="submit" name="_ajouter" value="Ajouter" class="vert" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php   
    // Si l'utilisateur appuie sur Ajouter
    if(isset($_POST["_ajouter"])) 
    {
        // Si certains champs n'ont pas été remplis (durée, date et efffctifs), assignation de la valeur NULL
        $duree = ($_POST["_duree"] == '' ? 'NULL' : $_POST["_duree"]);
        $date = ($_POST['_date'] == '' ? 'NULL' : "'".$_POST['_date']."'");
        $effMin = ($_POST["_effMin"] == '' ? 'NULL' : $_POST["_effMin"]);
        $effMax = ($_POST["_effMax"] == '' ? 'NULL' : $_POST["_effMax"]);
        $nomPro = str_replace("'","’",$_POST['_nomProjet']);
        $anneeGp = $anneeModule;
        
        // Si l'utilisateur inverse le min et le max :
        if($effMax < $effMin)
        {
            $temp = $effMax;
            $effMax = $effMin;
            $effMin = $temp;
        }
        
        // Requête pour vérifier si le projet existe déjà
        $ReqVerif = "SELECT * FROM PROJET WHERE idModulePere=$idModule AND nomProjet='$nomPro'";
        $TabVerif = mysqli_query($BDD, $ReqVerif);
        $LecVerif = mysqli_fetch_array($TabVerif);
        mysqli_free_result($TabVerif);
        
        // Si le nom du projet correspond à un autre projet déjà existant
        if($LecVerif != NULL)
        {
            // Message d'alerte
            ?>
                <script>alert("<?php echo htmlspecialchars('Veuillez donner un autre nom à votre projet !', ENT_QUOTES); ?>");
                window.location.href='ajouterProjet.php?module=<?php echo $idModule; ?>';</script>
            <?php
        }
        
        // S'il y a plusieurs promotions dans le module
        if(strlen($anneeModule) > 2)
        {
            if(isset($_POST["_annee1"])) { $A = "1A"; } else { $A = ""; }
            if(isset($_POST["_annee2"])) { $B = "2A"; } else { $B = ""; }
            if(isset($_POST["_annee3"])) { $C = "3A"; } else { $C = ""; }
            
            $anneeGp=$A.$B.$C;
            
            // Si l'utilisateur n'a pas rempli le champ, message d'erreur
            if($anneeGp == "")
            {
                // Message d'alerte
                ?>
                    <script>alert("<?php echo htmlspecialchars('Veuillez indiquer la/les promotion(s) qui peuvent créer des groupes dans le projet !', ENT_QUOTES); ?>");
                    window.location.href='ajouterProjet.php?module=<?php echo $idModule; ?>';</script>
                <?php
            }
        }
        
        // Si le projet n'existe pas et que l'utilisateur indique bien quelle promotion doit créer un groupe (si nécessaire)
        
        // Insertion du projet dans la base de données
        $ReqAjout = "INSERT INTO PROJET (nomProjet,dateLimite,duree, tailleGpMin, tailleGpMax, Candidature, creerGp, idModulePere) VALUES ('$nomPro',$date,$duree,$effMin,$effMax, '".$_POST["_candi"]."','$anneeGp', $idModule)"; 
        
        // Si l'utilisateur clique sur Parcourir pour ajouter un document
        if($anneeGp != "" && $LecVerif == NULL && isset($_FILES["_fichier"]) AND $_FILES["_fichier"]["error"] == 0)
        {
            $urlFichier = $_FILES["_fichier"]["name"];
            $nomFichier = $urlFichier;
            
            // Si l'utilisaeur nomme son document
            if($_POST["_nomDoc"] != "")
            {
                $nomFichier = $_POST["_nomDoc"];
            }
            
            mysqli_query($BDD, $ReqAjout);
            
            // Récupération de l'identifiant du projet qui vient d'être créé
            $ReqId = "SELECT idProjet FROM PROJET WHERE idModulePere=$idModule AND nomProjet='$nomPro'";
            $TabId = mysqli_query($BDD, $ReqId);
            $LecId = mysqli_fetch_array($TabId);
            mysqli_free_result($TabId);
            
            $idProjet = $LecId["idProjet"];
            
            // Ajout du fichier à la base de données et sur le serveur
            $ReqDoc = "INSERT INTO ANNEXE (nomAnnexe, urlAnnexe, idProjetAn) VALUES ('".$nomFichier."','".$urlFichier."',$idProjet)";
            
            if(move_uploaded_file($_FILES["_fichier"]["tmp_name"],"../../../annexe/".basename($urlFichier)) && mysqli_query($BDD,$ReqDoc))
            {
                // Pop-up affichant que le projet a été ajouté, et redirection vers la page du module
                ?>
                <script> alert("<?php echo htmlspecialchars('Le projet a bien été créé !', ENT_QUOTES); ?>");
                window.location.href='../module.php?module=<?php echo $idModule; ?>';</script>
                <?php
            }
            
            else 
            {
                // Message d'alerte
                ?>
                    <script>alert("<?php echo htmlspecialchars('Erreur !\nVotre document n’a pas pu être ajouté.', ENT_QUOTES); ?>")
                    window.location.href='ajouterProjet.php?module=<?php echo $idModule; ?>';</script>
                <?php
            }
        }
        
        elseif($anneeGp != "" && $LecVerif == NULL && mysqli_query($BDD, $ReqAjout))
        {
            // Pop-up affichant que le projet a été ajouté, et redirection vers la page du module
            ?>
            <script> alert("<?php echo htmlspecialchars('Le projet a bien été créé !', ENT_QUOTES); ?>");
            window.location.href='../module.php?module=<?php echo $idModule; ?>';</script>
            <?php
        }
        
        else 
        {
            // Message d'alerte
            ?>
                <script>alert("<?php echo htmlspecialchars('Erreur !\nVotre projet n’a pas pu être ajouté', ENT_QUOTES); ?>")
                window.location.href='ajouterProjet.php?module=<?php echo $idModule; ?>';</script>
            <?php
        }
    }
?>