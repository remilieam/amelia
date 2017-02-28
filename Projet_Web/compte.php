<!DOCTYPE html>

<?php
    require("BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: connexion.php");
    }
    
    // Récupération du statut
    $ReqSta = "SELECT * FROM CONNEXION WHERE login = '".$_COOKIE["_idf"]."'";
    $TabSta = mysqli_query($BDD,$ReqSta);
    $LecSta = mysqli_fetch_array($TabSta);
    mysqli_free_result($TabSta);
    
    $Statut = $LecSta["statut"];
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="stylesheet.css" />
        <link rel="shortcut icon" href="images/amelia.ico" />
        <title>AMÉLIA – Mon compte</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr>
                <td>Mon compte</td>
                <td class="right"><a href="<?php echo $Statut; ?>/accueil<?php echo ucfirst($Statut); ?>.php" class="blanc">Retour à l’accueil</a></td>
            </tr>
        </table>
        
        <section>
            
            <table class="large">
                <tr>
                    <td><h2>Mes messages</h2></td>
                    <td class="right">
                        <a href="compte/ecrireMessage.php" class="vert">Écrire un nouveau message</a>
                    </td>
                </tr>
            </table>
            
            <h3>Messages reçues non-lus</h3>
            
            <article>
<?php
    // Récupération de l’ensemble des messages non-lus (lu = 0) et non-supprimés (supprRecoi = 0)
    $ReqMgl = "SELECT * FROM MESSAGE WHERE lu = 0 AND supprRecoi = 0 AND loginRecoi = '".$_COOKIE["_idf"]."'";
    $TabMgl = mysqli_query($BDD,$ReqMgl);
    
    // S’il y a des messages non-lus et non-supprimés, on les affiche
    if(mysqli_num_rows($TabMgl) != 0)
    {
?>
                <table class="mail">
                    <tr><td class="mail">De :</td><td>Sujet :</td><td></td></tr>
<?php    
        while($LecMgl = mysqli_fetch_array($TabMgl))
        {
            // Récupération du nom et du prénom de l’envoyeur
            $ReqEnv = "SELECT * FROM CONNEXION WHERE login = '".$LecMgl["loginEnvoi"]."'";
            $TabEnv = mysqli_query($BDD,$ReqEnv);
            $LecEnv = mysqli_fetch_array($TabEnv);
            mysqli_free_result($TabEnv);
?>
                    <tr>
                        <td><b><?php echo $LecEnv["prenom"]." ".$LecEnv["nom"]; ?></b></td>
                        <td><b><a href="compte/lireMessage.php?message=<?php echo $LecMgl["idMessage"]; ?>"><?php echo $LecMgl["sujet"]; ?></a></b></td>
                        <td class="right"><a href="compte/supprimerMessage.php?message=<?php echo $LecMgl["idMessage"]; ?>" class="orange">Supprimer</a></td>
                    </tr>
<?php
        }
?>
                </table>
<?php
    }
    
    // Sinon, on affiche qu’il n’y a aucun message non-lu
    else
    {
?>
                
                <p>Aucun message non-lu…</p>
                
<?php
    }
?>
            </article>
            
            <h3>Messages reçus</h3>
            
            <article>
<?php
    // Récupération de l’ensemble des messages reçu (lu = 1) et non-supprimés (supprRecoi = 0)
    $ReqMgl = "SELECT * FROM MESSAGE WHERE lu <> 0 AND supprRecoi = 0 AND loginRecoi = '".$_COOKIE["_idf"]."'";
    $TabMgl = mysqli_query($BDD,$ReqMgl);
    
    // S’il y a des messages reçus et non-supprimés, on les affiche
    if(mysqli_num_rows($TabMgl) != 0)
    {
?>
                <table class="mail">
                    <tr><td class="mail">De :</td><td>Sujet :</td><td></td></tr>
<?php    
        while($LecMgl = mysqli_fetch_array($TabMgl))
        {
            // Récupération du nom et du prénom de l’envoyeur
            $ReqEnv = "SELECT * FROM CONNEXION WHERE login = '".$LecMgl["loginEnvoi"]."'";
            $TabEnv = mysqli_query($BDD,$ReqEnv);
            $LecEnv = mysqli_fetch_array($TabEnv);
            mysqli_free_result($TabEnv);
?>
                    <tr>
                        <td><?php echo $LecEnv["prenom"]." ".$LecEnv["nom"]; ?></td>
                        <td><a href="compte/lireMessage.php?message=<?php echo $LecMgl["idMessage"]; ?>"><?php echo $LecMgl["sujet"]; ?></a></td>
                        <td class="right"><a href="compte/supprimerMessage.php?message=<?php echo $LecMgl["idMessage"]; ?>" class="orange">Supprimer</a></td>
                    </tr>
<?php
        }
?>
                </table>
<?php
    }
    
    // Sinon, on affiche qu’il n’y a aucun message reçu
    else
    {
?>
                
                <p>Aucun message reçu…</p>
                
<?php
    }
?>
            </article>
            
            <h3>Messages envoyés</h3>
            
            <article>
<?php
    // Récupération de l’ensemble des messages envoyés et non-supprimés (supprEnvoi = 0)
    $ReqMgl = "SELECT * FROM MESSAGE WHERE supprEnvoi = 0 AND loginEnvoi = '".$_COOKIE["_idf"]."'";
    $TabMgl = mysqli_query($BDD,$ReqMgl);
    
    // S’il y a des messages envoyés et non-supprimés, on les affiche
    if(mysqli_num_rows($TabMgl) != 0)
    {
?>
                <table class="mail">
                    <tr><td class="mail">Pour :</td><td>Sujet :</td><td></td></tr>
<?php    
        while($LecMgl = mysqli_fetch_array($TabMgl))
        {
            // Récupération du nom et du prénom de l’envoyeur
            $ReqRec = "SELECT * FROM CONNEXION WHERE login = '".$LecMgl["loginRecoi"]."'";
            $TabRec = mysqli_query($BDD,$ReqRec);
            $LecRec = mysqli_fetch_array($TabRec);
            mysqli_free_result($TabRec);
?>
                    <tr>
                        <td><?php echo $LecRec["prenom"]." ".$LecRec["nom"]; ?></td>
                        <td><a href="compte/lireMessage.php?message=<?php echo $LecMgl["idMessage"]; ?>"><?php echo $LecMgl["sujet"]; ?></a></td>
                        <td class="right"><a href="compte/supprimerMessage.php?message=<?php echo $LecMgl["idMessage"]; ?>" class="orange">Supprimer</a></td>
                    </tr>
<?php
        }
?>
                </table>
<?php
    }
    
    // Sinon, on affiche qu’il n’y a aucun message envoyé
    else
    {
?>
                
                <p>Aucun message envoyé…</p>
                
<?php
    }
    
    mysqli_free_result($TabMgl);
?>
            </article>
            
            <table class="large">
                <tr>
                    <td><h2>Modifier le mot de passe</h2></td>
                </tr>
            </table>
            
            <article>
                <form method="POST">
                    <table class="large">
                        <tr>
                            <td>Mot de passe actuel :</td>
                            <td class="right"><input type="password" name="_mdpActu" size=48 required/></td>
                        </tr>
                        <tr>
                            <td>Nouveau mot de passe :</td>
                            <td class="right"><input type="password" name="_mdpNouv" size=48 required/></td>
                        </tr>
                        <tr>
                            <td>Vérication du nouveau mot de passe :</td>
                            <td class="right"><input type="password" name="_mdpConf" size=48 required/></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="right"><input type="submit" name="_valider" value="Valider" class="vert" /></td>
                        </tr>
                    </table>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_valider"]))
    {
        // Cas où le mot de passe actuel ne correspond pas ou où la confirmation et le nouveau mot de passe sont différents
        if($_COOKIE["_mdp"] != $_POST["_mdpActu"] || $_POST["_mdpNouv"] != $_POST["_mdpConf"])
        {
?>
            <script>
                alert("Saisie incorrecte ! Veuillez réessayer…");
            </script>
<?php
        }
        
        // Cas où tout va bien
        else
        {
            // Exécution de la requête
            $ReqMdp = "UPDATE CONNEXION SET mdp = '".$_POST["_mdpNouv"]."' WHERE login = '".$_COOKIE["_idf"]."'";
            
            // Cas où la requête est effectuée
            if(mysqli_query($BDD,$ReqMdp))
            {
                setcookie("_mdp",$_POST["_mdpNouv"],time()+60*60*24*365);
?>
                <script>
                    alert("Votre requête a été enregistrée avec succès !");
                </script>
<?php
            }
            
            // Cas dégénéré
            else 
            {
?>
                <script>
                    alert("Erreur ! Votre requête n’a pas pu être enregistrée.\nVeuillez réessayer.");
                </script>
<?php
            }
        }
    }
    
    mysqli_close($BDD);
?>