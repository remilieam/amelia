<!DOCTYPE html>

<?php
    require("../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../connexion.php");
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../stylesheet.css" />
        <link rel="shortcut icon" href="../images/amelia.ico" />
        <title>AMÉLIA – Accueil</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr>
                <td>Accueil</td>
                <td class="right"></td>
            </tr>
        </table>
        
        <section>
            
            <table class="large">
                <tr>
                    <td><h2>Mes candidatures</h2></td>
                    <td class="right">
                        <a href="accueilEleve/ajouterCandidature.php" class="vert">Ajouter une candidature</a>
                        <a href="accueilEleve/etatCandidature.php" class="bleu">État des candidatures</a>
                    </td>
                </tr>
            </table>
            
            <article>
<?php
    // Récupération de l’ensemble des candidatures que l’élève a écrites
    $ReqCdt = "SELECT DISTINCT * FROM CANDIDATURE, CONNEXION, PROJET, MODULE, GROUPE WHERE login = '".$_COOKIE["_idf"]."' AND loginEleveCa = login AND idGroupeCa = idGroupe AND idProjetGr = idProjet AND idModulePere = idModule";
    $TabCdt = mysqli_query($BDD,$ReqCdt);
    
    // Vérification pour savoir s’il y a des candidatures
    if(mysqli_num_rows($TabCdt) != 0)
    {
        // Si oui, on les affiche
        while($LecCdt = mysqli_fetch_array($TabCdt))
        {
?>
                <p>
                    <a href="accueilEleve/etatCandidature/voirCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>">
                        <?php echo $LecCdt["nomModule"]; ?> > <?php echo $LecCdt["nomProjet"]; ?> > <?php echo $LecCdt["nomGroupe"]; ?>
                    </a>
                </p>
<?php
        }
    }
    
    // Sinon, on affiche qu’il n’y a aucune candidature
    else 
    {
?>
                <p>Aucune candidature…</p>
<?php
    }
    
    mysqli_free_result($TabCdt);
?>
            </article>
            
            <table class="large">
                <tr>
                    <td><h2>Modules</h2></td>
                </tr>
            </table>
            
            <article>
                
<?php
    // Récupération de l’ensemble des modules dont l’année correspond avec celle de l’élève
    $ReqMdl = "SELECT * FROM CONNEXION, MODULE WHERE login = '".$_COOKIE["_idf"]."' AND anneeModule LIKE '%".$_SESSION["_annee"]."%' ORDER BY nomModule";
    $TabMdl = mysqli_query($BDD,$ReqMdl);
    
    // Vérification pour savoir s’il y a des modules auxquels l’élève a accès
    if(mysqli_num_rows($TabMdl) != 0)
    {
        // Si oui, on les affiche
        while($LecMdl = mysqli_fetch_array($TabMdl))
        {
?>
                <p><a href="accueilEleve/module.php?module=<?php echo $LecMdl["idModule"]; ?>"><?php echo $LecMdl["nomModule"]; ?></a></p>
<?php
            // Récupération et affichage du nom et du prénom de l’enseignant responsable du module
            $ReqRsp = "SELECT * FROM CONNEXION WHERE login = '".$LecMdl["loginEnseiResp"]."'";
            $TabRsp = mysqli_query($BDD,$ReqRsp);
            $LecRsp = mysqli_fetch_array($TabRsp);
            mysqli_free_result($TabRsp);
?>

                <table class="large">
                    <tr><td class="projet">Responsable :</td><td><?php echo $LecRsp["prenom"]." ".$LecRsp["nom"]; ?></td></tr>
                    <tr class="projet">
                        <td>Projets :</td>
                        <td><?php
            $i = 0;
            
            // Récupération et affichage des éventuels projets à l’intérieur du module
            $ReqPrj = "SELECT * FROM PROJET WHERE idModulePere = ".$LecMdl["idModule"];
            $TabPrj = mysqli_query($BDD,$ReqPrj);
            
            while($LecPrj = mysqli_fetch_array($TabPrj))
            {
                echo $LecPrj["nomProjet"]."<br/>";
            }
            
            mysqli_free_result($TabPrj);
                        ?></td>
                    </tr>
                </table>
                
<?php
        }
    }
    
    // Sinon, on affiche qu’il n’y a aucun module disponible
    else  
    {
?>
                <p>Aucun module…</p>
<?php
    }
    
    mysqli_free_result($TabMdl);
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