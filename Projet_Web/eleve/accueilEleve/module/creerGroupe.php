<!DOCTYPE html> <!--EULAID:O15_RTM_VL.1_RTM_FR-->

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
        $ReqPrj = "SELECT * FROM PROJET, MODULE WHERE anneeModule LIKE '%".$_SESSION["_annee"]."%' AND idModule = idModulePere AND idProjet = ".$_GET["projet"];
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
            window.location.href = "../../accueilEleve.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
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
                <td class="header"><a href="../../accueilEleve.php">Accueil</a> > <a href="../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <?php echo $nomProjet; ?></td>
                <td class="right"><a href="../module.php?module=<?php echo $idModule; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Création d’un nouveau groupe</h2>
            
            <article>
                <form method="POST">
                    <p>
                        Entrer le nom du groupe :
                        <input type="text" name="_nomGroupe" required />
                    </p>
                    <p>
                        Écrivez, si nécessaire, un texte succint pour décrire votre projet (objectifs, matières abordées, etc.) :
                    </p>
                    <textarea name="_texteGroupe" rows=5></textarea>
                    <p>
                        Cocher les membres que vous voulez ajouter au groupe :
                    </p>
<?php
    // Récupération de tous les élèves de la même année que l’élève, sauf lui-même
    $ReqMbr = "SELECT * FROM CONNEXION WHERE login <> '".$_COOKIE["_idf"]."' AND anneeEleve = (SELECT anneeEleve FROM CONNEXION WHERE login = '".$_COOKIE["_idf"]."') AND login NOT IN (SELECT loginEleveAp FROM APPARTIENT, GROUPE, PROJET WHERE idGroupeAP = idGroupe AND idProjetGr = idProjet AND idProjet = ".$idProjet.")";
    $TabMbr = mysqli_query($BDD,$ReqMbr);
    
    $i = 0;
    
    // Ajout de chaque élève dans la liste à cocher
    while($LecMbr = mysqli_fetch_array($TabMbr))
    {
?>
                    <p><input type="checkbox" name="_membre_<?php echo $i; ?>" value="<?php echo $LecMbr["login"]; ?>"/> <?php echo $LecMbr["prenom"]." ".$LecMbr["nom"]; ?></p>
<?php
        $i += 1;
    }
    
    mysqli_free_result($TabMbr);
    
    $ReqCdt = "SELECT * FROM PROJET, MODULE WHERE idModule = idModulePere AND idProjet = ".$idProjet;
    $TabCdt = mysqli_query($BDD,$ReqCdt);
    $LecCdt = mysqli_fetch_array($TabCdt);
    mysqli_free_result($TabCdt);
    
    if($LecCdt["candidature"] && strlen($LecCdt["anneeModule"]) > 2)
    {
?>
                    <p>Cocher les promotions qui peuvent candidater dans le groupe que vous créez :</p>
<?php
        if(substr($LecCdt["anneeModule"],0,2) == "1A")
        {
?>
                    <p><input type="checkbox" name="_1A" value="1A" /> 1A</p>
<?php
        }
        
        if(substr($LecCdt["anneeModule"],0,2) == "2A" || substr($LecCdt["anneeModule"],2,2) == "2A")
        {
?>
                    <p><input type="checkbox" name="_2A" value="2A" /> 2A</p>
<?php
        }
        
        if(substr($LecCdt["anneeModule"],0,2) == "3A" || substr($LecCdt["anneeModule"],2,2) == "3A" || substr($LecCdt["anneeModule"],4,2) == "3A")
        {
?>
                    <p><input type="checkbox" name="_3A" value="3A" /> 3A</p>
<?php
        }
    }
    
    elseif($LecCdt["candidature"] && strlen($LecCdt["anneeModule"]) == 2)
    {
?>
                    <input type="hidden" name="_<?php echo $LecCdt["anneeModule"]; ?>" value="<?php echo $LecCdt["anneeModule"]; ?>" />
<?php
    }
?>
                    <p class="right"><input type="submit" name="_valider" value="Valider" class="vert" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_valider"]))
    {
        // Gestion des apostrophes
        $message = $_POST["_texteGroupe"];
        $message = str_replace("'","’",$message);
        
        // Chaîne pour les années qui peuvent candidater
        if(isset($_POST["_1A"])) { $A = "1A"; } else { $A = ""; }
        if(isset($_POST["_2A"])) { $B = "2A"; } else { $B = ""; }
        if(isset($_POST["_3A"])) { $C = "3A"; } else { $C = ""; }
        
        // Vérification qu’on peut bien candidater dans les groupes où on doit accepter des candidatures
        if($A.$B.$C == "" && $LecCdt["candidature"])
        {
?>
            <script>
                alert("Erreur ! Vous devez autorisez des promotions à candidater.");
                window.location.href = "creerGroupe.php?projet=<?php echo $idProjet; ?>";
            </script>
<?php
        }
        
        else 
        {
            // Création du grouoe
            $ReqGrp = "INSERT INTO GROUPE (nomGroupe, idProjetGr, description, anneeCandid) VALUES ('".str_replace("'","’",$_POST["_nomGroupe"])."',".$idProjet.",'".$message."','".$A.$B.$C."')";
            
            // Si le groupe est créé, on rajoute les élèves cochés et l’élève lui-même
            if(mysqli_query($BDD,$ReqGrp))
            {
                // Récupération de l’identifiant du groupe (max. car dernier créé et auto-incrementations)
                $ReqIdf = "SELECT * FROM GROUPE WHERE idGroupe = (SELECT MAX(idGroupe) FROM GROUPE)";
                $TabIdf = mysqli_query($BDD,$ReqIdf);
                $LecIdf = mysqli_fetch_array($TabIdf);
                mysqli_free_result($TabIdf);
                
                $Nb = 0;
                
                // Récupération des élèves cochés dans un tableau
                for($j = 0; $j < $i; $j++)
                {
                    if(isset($_POST["_membre_$j"]))
                    {
                        $Eleve[$Nb] = $_POST["_membre_$j"];
                        $Nb += 1;
                    }
                }
                
                for($k = 0; $k < $Nb; $k++)
				{
					$ReqElv = "INSERT INTO APPARTIENT (loginEleveAp,idGroupeAp) VALUES ('".$Eleve[$k]."',".$LecIdf["idGroupe"].")";
					
					// En cas d’échec de l’ajout d’un des futurs membres, on annule toutes les actions précédentes
					if(!mysqli_query($BDD,$ReqElv)) 
					{
						// Suppression du groupe
						$ReqOte = "DELETE FROM GROUPE WHERE idGroupe = ".$LecIdf["idGroupe"];
						mysqli_query($BDD,$ReqOte);
						
						// Suppression des élèves déjà ajoutés
						$ReqSpr = "DELETE FROM APPARTIENT WHERE idGroupeAp = ".$LecIdf["idGroupe"];
						mysqli_query($BDD,$ReqSpr);
?>
						<script>
							alert("Erreur ! Votre requête n’a pas pu être effectuée :\nTous les élèves n’ont pas pu être ajoutés dans le groupe.\nVeuillez réessayer.");
							window.location.href = "creerGroupe.php?projet=<?php echo $idProjet; ?>";
						</script>
<?php
					}
				}
				
				// Ajout de l’élève au groupe (en tant que propriétaire [2])
				$ReqElv = "INSERT INTO APPARTIENT (loginEleveAp,idGroupeAp, admin) VALUES ('".$_COOKIE["_idf"]."', ".$LecIdf["idGroupe"].", 2)";
				
				// En cas d’échec de l’ajout de l’élève, on annule toutes les actions précédentes
				if(!mysqli_query($BDD,$ReqElv)) 
				{
					// Suppression du groupe
					$ReqOte = "DELETE FROM GROUPE WHERE idGroupe = ".$LecIdf["idGroupe"];
					mysqli_query($BDD,$ReqOte);
					
					// Suppression de tous les membres
					$ReqSpr = "DELETE FROM APPARTIENT WHERE idGroupeAp = ".$LecIdf["idGroupe"];
					mysqli_query($BDD,$ReqSpr);
?>
					<script>
						alert("Erreur ! Votre requête n’a pas pu être effectuée :\nTous les élèves n’ont pas pu être ajoutés dans le groupe.\nVeuillez réessayer.");
						window.location.href = "creerGroupe.php?projet=<?php echo $idProjet; ?>";
					</script>
<?php
				}
?>
				<script>
					alert("Votre requête a bien été effectuée !");
					window.location.href = "projet.php?projet=<?php echo $idProjet; ?>";
				</script>
<?php
            }
            
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre requête n’a pas pu être effectuée.\nVeuillez réessayer.");
                        window.location.href = "creerGroupe.php?projet=<?php echo $idProjet; ?>";
                </script>
<?php
            }
        }
    }
    
    mysqli_close($BDD);
?>
