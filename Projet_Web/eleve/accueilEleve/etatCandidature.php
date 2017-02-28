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
        <title>AMÉLIA – État des candidatures</title>
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
                <td><a href="../accueilEleve.php">Accueil</a> > État des candidatures</td>
                <td class="right"><a href="../accueilEleve.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Candidatures acceptées</h2>
            
            <article>
<?php
    // Récupération de l’ensemble des candidatures acceptées (etat = 1)
    $ReqCdt = "SELECT * FROM CANDIDATURE, GROUPE, PROJET, MODULE WHERE idGroupeCa = idGroupe AND idProjetGr = idProjet AND idModulePere = idModule AND etat = 1 AND loginEleveCa = '".$_COOKIE["_idf"]."'";
    $TabCdt = mysqli_query($BDD,$ReqCdt);
    
    // Vérification pour savoir s’il y a des candidatures acceptées
    if(mysqli_num_rows($TabCdt) != 0)
    {
        // Si oui, on les affiche
        while($LecCdt = mysqli_fetch_array($TabCdt))
        {
?>
                <p>
                    <?php echo $LecCdt["nomModule"]." > ".$LecCdt["nomProjet"]." > ".$LecCdt["nomGroupe"]; ?> 
                </p>
                <p class="right">
                    <a href="etatCandidature/voirCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="bleu">Voir</a>
                    <a href="etatCandidature/accepterProposition.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="vert">Accepter</a>
                    <a href="etatCandidature/refuserProposition.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="orange">Refuser</a>
                </p>
<?php
        }
    }
    
    // Si non, on affiche qu’il n’y a aucune candidature acceptée
    else 
    {
?>
                <p>Aucune candidature acceptée…</p>
<?php
    }
?>
            </article>
            
            <h2>Candidatures refusées</h2>
            
            <article>
<?php
    // Récupération de l’ensemble des candidatures refusées (etat = 2)
    $ReqCdt = "SELECT * FROM CANDIDATURE, GROUPE, PROJET, MODULE WHERE idGroupeCa = idGroupe AND idProjetGr = idProjet AND idModulePere = idModule AND etat = 2 AND loginEleveCa = '".$_COOKIE["_idf"]."'";
    $TabCdt = mysqli_query($BDD,$ReqCdt);
    
    // Vérification pour savoir s’il y a des candidatures refusées
    if(mysqli_num_rows($TabCdt) != 0)
    {
        // Si oui, on les affiche
        while($LecCdt = mysqli_fetch_array($TabCdt))
        {
?>
                <p>
                    <?php echo $LecCdt["nomModule"]." > ".$LecCdt["nomProjet"]." > ".$LecCdt["nomGroupe"]; ?> 
                </p>
                <p class="right">
                    <a href="etatCandidature/voirCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="bleu">Voir</a>
                    <a href="etatCandidature/supprimerCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="orange">Supprimer</a>
                </p>
<?php
        }
    }
    
    // Si non, on affiche qu’il n’y a aucune candidature refusé
    else 
    {
?>
                <p>Aucune candidature refusée…</p>
<?php
    }
?>
            </article>
            
            <h2>Candidatures sans réponse</h2>
            
            <article>
<?php
    // Récupération de l’ensemble des candidatures sans réponse (etat = 0)
    $ReqCdt = "SELECT * FROM CANDIDATURE, GROUPE, PROJET, MODULE WHERE idGroupeCa = idGroupe AND idProjetGr = idProjet AND idModulePere = idModule AND etat = 0 AND loginEleveCa = '".$_COOKIE["_idf"]."'";
    $TabCdt = mysqli_query($BDD,$ReqCdt);
    
    // Vérification pour savoir s’il y a des candidatures sans réponse
    if(mysqli_num_rows($TabCdt) != 0)
    {
        // Si oui, on les affiche
        while($LecCdt = mysqli_fetch_array($TabCdt))
        {
?>
                <p>
                    <?php echo $LecCdt["nomModule"]." > ".$LecCdt["nomProjet"]." > ".$LecCdt["nomGroupe"]; ?> 
                </p>
                <p class="right">
                    <a href="etatCandidature/voirCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="bleu">Voir</a>
                    <a href="etatCandidature/modifierCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="jaune">Modifier</a>
                    <a href="etatCandidature/supprimerCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="orange">Supprimer</a>
                </p>
<?php
        }
    }
    
    // Si non, on affiche qu’il n’y a aucune candidature sans réponse
    else 
    {
?>
                <p>Aucune candidature sans réponse…</p>
<?php
    }
    
    mysqli_free_result($TabCdt);
?>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    mysqli_close($BDD);
?>