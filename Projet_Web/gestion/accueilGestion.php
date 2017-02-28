<!DOCTYPE html>

<?php
    require("../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
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
            
            <h2>Modules 1A</h2>
                
            <article>
<?php
    // Récupération de l’ensemble des modules
    $ReqMdl = "SELECT * FROM MODULE WHERE anneeModule LIKE '%1A%' ORDER BY nomModule";
    $TabMdl = mysqli_query($BDD,$ReqMdl);
    
    // Vérification pour savoir s’il y a des modules
    if(mysqli_num_rows($TabMdl) != 0)
    {
        // Si oui, on les affiche
        while($LecMdl = mysqli_fetch_array($TabMdl)) // Affichage des modules et de leurs caractéristiques (description, responsable, projets)
        {
?>
                <table class="large">
                    <tr>
                        <td><a href="accueilGestion/module.php?module=<?php echo $LecMdl["idModule"]; ?>"><?php echo $LecMdl["nomModule"]; ?></a></td>
                        <td class="right"><a href="accueilGestion/supprimerModule.php?module=<?php echo $LecMdl["idModule"]; ?>" class="orange">Supprimer le module</a></td>
                    </tr>
                </table>
<?php
            // Récupération et affichage du nom et du prénom de l’enseignant responsable du module
            $ReqRsp = "SELECT * FROM CONNEXION WHERE login = '".$LecMdl["loginEnseiResp"]."'";
            $TabRsp = mysqli_query($BDD,$ReqRsp);
            $LecRsp = mysqli_fetch_array($TabRsp);
?>
                <table class="large">
                    <tr><td class="projet">Responsable :</td><td><?php echo $LecRsp["prenom"]." ".$LecRsp["nom"]; ?></td></tr>
                    <tr class="projet">
                        <td>Projets :</td>
                        <td><?php
            // Récupération et affichage des éventuels projets à l’intérieur du module
            $ReqPrj = "SELECT * FROM PROJET WHERE idModulePere = ".$LecMdl["idModule"];
            $TabPrj = mysqli_query($BDD,$ReqPrj);
            
            while($LecPrj = mysqli_fetch_array($TabPrj))
            {
                echo $LecPrj["nomProjet"]."<br/>";
            }
                        ?></td>
                    </tr>
                </table>
<?php
        }
        
        mysqli_free_result($TabPrj);
        mysqli_free_result($TabRsp);
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
            
            <h2>Modules 2A</h2>
                
            <article>
<?php
    // Récupération de l’ensemble des modules
    $ReqMdl = "SELECT * FROM MODULE WHERE anneeModule LIKE '%2A%' ORDER BY nomModule";
    $TabMdl = mysqli_query($BDD,$ReqMdl);
    
    // Vérification pour savoir s’il y a des modules
    if(mysqli_num_rows($TabMdl) != 0)
    {
        // Si oui, on les affiche
        while($LecMdl = mysqli_fetch_array($TabMdl)) // Affichage des modules et de leurs caractéristiques (description, responsable, projets)
        {
?>
                <table class="large">
                    <tr>
                        <td><a href="accueilGestion/module.php?module=<?php echo $LecMdl["idModule"]; ?>"><?php echo $LecMdl["nomModule"]; ?></a></td>
                        <td class="right"><a href="accueilGestion/supprimerModule.php?module=<?php echo $LecMdl["idModule"]; ?>" class="orange">Supprimer le module</a></td>
                    </tr>
                </table>
<?php
            // Récupération et affichage du nom et du prénom de l’enseignant responsable du module
            $ReqRsp = "SELECT * FROM CONNEXION WHERE login = '".$LecMdl["loginEnseiResp"]."'";
            $TabRsp = mysqli_query($BDD,$ReqRsp);
            $LecRsp = mysqli_fetch_array($TabRsp);
?>
                <table class="large">
                    <tr><td class="projet">Responsable :</td><td><?php echo $LecRsp["prenom"]." ".$LecRsp["nom"]; ?></td></tr>
                    <tr class="projet">
                        <td>Projets :</td>
                        <td><?php
            // Récupération et affichage des éventuels projets à l’intérieur du module
            $ReqPrj = "SELECT * FROM PROJET WHERE idModulePere = ".$LecMdl["idModule"];
            $TabPrj = mysqli_query($BDD,$ReqPrj);
            
            while($LecPrj = mysqli_fetch_array($TabPrj))
            {
                echo $LecPrj["nomProjet"]."<br/>";
            }
                        ?></td>
                    </tr>
                </table>
<?php
        }
    
        mysqli_free_result($TabPrj);
        mysqli_free_result($TabRsp);
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
            
            <h2>Modules 3A</h2>
                
            <article>
<?php
    // Récupération de l’ensemble des modules
    $ReqMdl = "SELECT * FROM MODULE WHERE anneeModule LIKE '%3A%' ORDER BY nomModule";
    $TabMdl = mysqli_query($BDD,$ReqMdl);
    
    // Vérification pour savoir s’il y a des modules
    if(mysqli_num_rows($TabMdl) != 0)
    {
        // Si oui, on les affiche
        while($LecMdl = mysqli_fetch_array($TabMdl)) // Affichage des modules et de leurs caractéristiques (description, responsable, projets)
        {
?>
                <table class="large">
                    <tr>
                        <td><a href="accueilGestion/module.php?module=<?php echo $LecMdl["idModule"]; ?>"><?php echo $LecMdl["nomModule"]; ?></a></td>
                        <td class="right"><a href="accueilGestion/supprimerModule.php?module=<?php echo $LecMdl["idModule"]; ?>" class="orange">Supprimer le module</a></td>
                    </tr>
                </table>
<?php
            // Récupération et affichage du nom et du prénom de l’enseignant responsable du module
            $ReqRsp = "SELECT * FROM CONNEXION WHERE login = '".$LecMdl["loginEnseiResp"]."'";
            $TabRsp = mysqli_query($BDD,$ReqRsp);
            $LecRsp = mysqli_fetch_array($TabRsp);
?>
                <table class="large">
                    <tr><td class="projet">Responsable :</td><td><?php echo $LecRsp["prenom"]." ".$LecRsp["nom"]; ?></td></tr>
                    <tr class="projet">
                        <td>Projets :</td>
                        <td><?php
            // Récupération et affichage des éventuels projets à l’intérieur du module
            $ReqPrj = "SELECT * FROM PROJET WHERE idModulePere = ".$LecMdl["idModule"];
            $TabPrj = mysqli_query($BDD,$ReqPrj);
            
            while($LecPrj = mysqli_fetch_array($TabPrj))
            {
                echo $LecPrj["nomProjet"]."<br/>";
            }
                        ?></td>
                    </tr>
                </table>
<?php
        }
    
        mysqli_free_result($TabPrj);
        mysqli_free_result($TabRsp);
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