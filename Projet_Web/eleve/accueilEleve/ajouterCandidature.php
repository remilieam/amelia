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
        <link rel="stylesheet" href="../../stylesheet.css" />
        <link rel="shortcut icon" href="../../images/amelia.ico" />
        <title>AMÉLIA – Ajouter une candidature</title>
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
        
        <table class="header">
            <tr>
                <td><a href="../accueilEleve.php">Accueil</a> > Ajouter une candidature</td>
                <td class="right"><a href="../accueilEleve.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
<?php
    // Première étape : Choix du module
    if(empty($_POST["_suivant1"]) || !empty($_POST["_precedent2"]))
    {
?>
            
            <h2>Ajouter une candidature – Première étape</h2>
            
            <article>
<?php
        // Récupération de l’ensemble des modules possédant des projets qui autorisent les candidatures
        $ReqMdl = "SELECT * FROM MODULE, PROJET, GROUPE WHERE idModule = idModulePere AND idProjet = idProjetGr AND anneeCandid LIKE '%".$_SESSION["_annee"]."%' GROUP BY idModule";
        $TabMdl = mysqli_query($BDD,$ReqMdl);
        
        if(mysqli_num_rows($TabMdl) != 0)
        {
?>
                <form method="POST">
                    <p>
                        Choisissez le module d’enseignement :
                        <select name="_module">
<?php
            // Ajout des modules récupérés à la liste déroulante
            while($LecMdl = mysqli_fetch_array($TabMdl))
            {
?>
                            <option value="<?php echo $LecMdl["idModule"]; ?>"><?php echo $LecMdl["nomModule"]; ?></option>
<?php
            }
?>
                        </select>
                    </p>
                    <p class="right">
                        <input type="submit" name="_suivant1" value="Suivant" class="vert" />
                    </p>
                </form>
<?php
        }
        
        else 
        {
?>
                <form method="POST">
                    <p>Vous ne pouvez candidater dans aucun groupe pour le moment.</p>
                    <p class="right">
                        <a href="../accueilEleve.php" class="vert">OK</a>
                    </p>
                </form>
<?php
        }
        
        mysqli_free_result($TabMdl);
?>
            </article>
<?php
    }
    
    // Deuxième étape : Choix du projet
    elseif(empty($_POST["_suivant2"]) || !empty($_POST["_precedent3"]))
    {
?>
            
            <h2>Ajouter une candidature – Deuxième étape</h2>
            
            <article>
<?php
        // Récupération de l’ensemble de projets autorisant les candidatures et correspondant au module choisi à l’étape 1
        $ReqPrj = "SELECT * FROM PROJET WHERE candidature <> 0 AND idModulePere = ".$_POST["_module"];
        $TabPrj = mysqli_query($BDD,$ReqPrj);
        
        if(mysqli_num_rows($TabPrj) != 0)
        {
?>
                <form method="POST">
                    <p>
                        Choisissez le projet :
                        <select name="_projet">
<?php
            // Ajout des projets récupérés à la liste déroulante
            while($LecPrj = mysqli_fetch_array($TabPrj))
            {
                $Tabl[$LecPrj["idProjet"]] = $LecPrj["candidature"];
?>
                            <option value="<?php echo $LecPrj["idProjet"]; ?>"><?php echo $LecPrj["nomProjet"]; ?></option>
<?php
            }
?>
                        </select>
                    </p>
                    <p class="right">
                        <input type="hidden" name="_suivant1" value="x"/>
                        <input type="hidden" name="_module" value="<?php echo $_POST["_module"];?>"/>
                        <input type="submit" name="_precedent2" value="Retour" class="jaune" />
                        <input type="submit" name="_suivant2" value="Suivant" class="vert" />
                    </p>
                </form>
<?php
        }
        
        else 
        {
?>
                <form method="POST">
                    <p>Vous ne pouvez candidater dans aucun groupe pour le moment.</p>
                    <p class="right">
                        <input type="hidden" name="_suivant1" value="x"/>
                        <input type="hidden" name="_module" value="<?php echo $_POST["_module"];?>"/>
                        <input type="submit" name="_precedent2" value="Retour" class="jaune" />
                        <a href="../accueilEleve.php" class="vert">OK</a>
                    </p>
                </form>
<?php
        }
        
        mysqli_free_result($TabPrj);
?>
            </article>
<?php
    }
    
    // Troisième étape : Choix du groupe
    elseif(empty($_POST["_suivant3"]) || !empty($_POST["_precedent4"]))
    {
?>
            
            <h2>Ajouter une candidature – Troisième étape</h2>
            
<?php
        // Récupération de l’ensemble des groupes correspondant au projet choisi à l’étape 2
        $ReqGrp = "SELECT * FROM GROUPE WHERE validation = 0 AND idProjetGr = ".$_POST["_projet"];
        $TabGrp = mysqli_query($BDD,$ReqGrp);
        
        if(mysqli_num_rows($TabGrp) != 0)
        {
?>
            <form method="POST">
                <p>Choisissez un groupe :</p>
<?php
            // Si oui, on les affiche
            while($LecGrp = mysqli_fetch_array($TabGrp))
            {
                // Récupération du noms et prénoms des élèves du groupe
                $ReqElv = "SELECT * FROM CONNEXION, APPARTIENT, GROUPE WHERE login = loginEleveAp AND idGroupeAp = idGroupe AND idGroupe = ".$LecGrp["idGroupe"];
                $TabElv = mysqli_query($BDD,$ReqElv);
?>
                <h3>
                    <input type="radio" name="_groupe" value="<?php echo $LecGrp["idGroupe"]; ?>" id="<?php echo $LecGrp["idGroupe"]; ?>" checked />
                    <label for="<?php echo $LecGrp["idGroupe"]; ?>"><?php echo $LecGrp["nomGroupe"]; ?></label>
                </h3>
                <article>
                    <table class="large">
<?php
                // Affichage éventuel de la description du groupe
                if($LecGrp["description"] != NULL || $LecGrp["description"] != "")
                {
                    $description = $LecGrp["description"];
                    $description = str_replace(' "',' “',$description);
                    $description = str_replace('"','”',$description);
?>
                        <tr class="projet">
                            <td class="projet">Description :</td>
                            <td><?php echo $description; ?></td>
                        </tr>
<?php
                }
?>
                        <tr class="projet">
                            <td>Élèves :</td>
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
                    </table>
                </article>
<?php
            }
?>
                <p class="right">
                    <input type="hidden" name="_suivant1" value="x"/>
                    <input type="hidden" name="_suivant2" value="x"/>
                    <input type="hidden" name="_module" value="<?php echo $_POST["_module"]; ?>"/>
                    <input type="hidden" name="_projet" value="<?php echo $_POST["_projet"]; ?>"/>
                    <input type="submit" name="_precedent3" value="Retour" class="jaune" />
                    <input type="submit" name="_suivant3" value="Suivant" class="vert" />
                </p>
            </form>
<?php
        }
        
        else 
        {
?>
            <article>
                <form method="POST">
                    <p>Vous ne pouvez candidater dans aucun groupe pour le moment.</p>
                    <p class="right">
                        <input type="hidden" name="_suivant1" value="x"/>
                        <input type="hidden" name="_suivant2" value="x"/>
                        <input type="hidden" name="_module" value="<?php echo $_POST["_module"]; ?>"/>
                        <input type="hidden" name="_projet" value="<?php echo $_POST["_projet"]; ?>"/>
                        <input type="submit" name="_precedent3" value="Retour" class="jaune" />
                        <a href="../accueilEleve.php" class="vert">OK</a>
                    </p>
                </form>
            </article>
<?php
        }

    }
    
    // Quatrième étape : Motivation
    else 
    {
?>
            
            <h2>Ajouter une candidature – Quatrième étape</h2>
            
            <article>
                <form method="POST">
                    <p>Écrivez ici votre message (raison de votre candidature, motivation, compétences, etc.) :</p>
                    <textarea name="_message" rows=5></textarea>
                    <p class="right">
                        <input type="hidden" name="_suivant1" value="x"/>
                        <input type="hidden" name="_suivant2" value="x"/>
                        <input type="hidden" name="_suivant3" value="x"/>
                        <input type="hidden" name="_module" value="<?php echo $_POST["_module"]; ?>"/>
                        <input type="hidden" name="_projet" value="<?php echo $_POST["_projet"]; ?>"/>
                        <input type="hidden" name="_groupe" value="<?php echo $_POST["_groupe"]; ?>"/>
                        <input type="submit" name="_precedent4" value="Retour" class="jaune" />
                        <input type="submit" name="_envoyer" value="Envoyer" class="vert" />
                    </p>
                </form>
            </article>
<?php
    }
?>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_envoyer"]))
    {
        // Vérification que l’élève n’a pas déjà candidaté au groupe choisi
        $ReqVerif = "SELECT * FROM CANDIDATURE WHERE loginEleveCa = '".$_COOKIE["_idf"]."' AND idGroupeCa = ".$_POST["_groupe"];
        $TabVerif = mysqli_query($BDD,$ReqVerif);
        $Verif1 = mysqli_fetch_array($TabVerif);
        mysqli_free_result($TabVerif);
        
        // Vérification que l’élève n’appartient pas déjà à un groupe dans le projet choisi
        $ReqVerif = "SELECT * FROM APPARTIENT, GROUPE WHERE loginEleveAp = '".$_COOKIE["_idf"]."' AND idProjetGr = ".$_POST["_projet"]." AND idGroupe = idGroupeAp";
        $TabVerif = mysqli_query($BDD,$ReqVerif);
        $Verif2 = mysqli_fetch_array($TabVerif);
        mysqli_free_result($TabVerif);
        
        // Si les conditions sont vérifiées, on ajoute la candidature
        if($Verif1 == NULL && $Verif2 == NULL)
        {
            $message = $_POST["_message"];
            $message = str_replace("'","’",$message);
            
            $ReqCdt = "INSERT INTO CANDIDATURE(texte,loginEleveCa,idGroupeCa) VALUES ('".$message."','".$_COOKIE["_idf"]."',".$_POST["_groupe"].")";
            
            if(mysqli_query($BDD,$ReqCdt))
            {
?>
                <script>
                    alert("Votre candidature a bien été envoyée !");
                    window.location.href = "../accueilEleve.php";
                </script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre candidature n’a pas pu être envoyé.\nVeuillez réessayer.");
                    window.location.href = "ajouterCandidatures.php";
                </script>
<?php
            }
        }
        
        // Sinon, on affiche un message d’erreur
        else
        {
?>
            <script>
                alert("Erreur !\nVous appartenez déjà à un groupe du projet dans lequel vous candidatez\nou vous avez déjà candidaté dans le groupe choisi.");
                window.location.href = "../accueilEleve.php";
            </script>
<?php
        }
    }

    mysqli_close($BDD);
?>