<!DOCTYPE html>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="stylesheet.css" />
        <link rel="shortcut icon" href="images/amelia.ico" />
        <title>AMÉLIA – Connexion</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right"><img height=100px src="images/ensc.png" alt="Logo de l’ENSC" title="Logo de l’ENSC" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="right"></td>
                </tr>
            </table>
        </header>
        
        <section class="connect">
            
            <h2 class="connect">Connexion</h2>
            
            <form method="POST" action="connexion.php">
                
                <table class="connect">
                    <tr>
                        <td>Identifiant :</td>
                        <td><input type="text" name="_idf" required /></td>
                    </tr>
                    <tr>
                        <td>Mot de passe :</td>
                        <td><input type="password" name="_mdp" required /></td>
                    </tr>
                    <tr>
                        <td><a href="oubli.php">Mot de passe oublié ?</a></td>
                        <td class="right"><input type="submit" name="_seConnecter" value="Se connecter" class="vert" /></td>
                    </tr>
                </table>
                
            </form>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="contactConnexion.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_seConnecter"]))
    {
        session_start();
        setcookie("_idf",$_POST["_idf"],time()+60*60*24*365);
        setcookie("_mdp",$_POST["_mdp"],time()+60*60*24*365);
        
        require ("BDD.php");
        mysqli_set_charset($BDD, "utf8");
        
        // Vérification de l’authentification
        $RqtRecup = "SELECT * FROM CONNEXION WHERE login = '".$_POST["_idf"]."' AND mdp = '".$_POST["_mdp"]."'";
        $TabRecup = mysqli_query($BDD, $RqtRecup);
        $LecRecup = mysqli_fetch_array($TabRecup);
        mysqli_free_result($TabRecup);
        
        // Récupération du nom, prénom et statut de la personne connectée
        $_SESSION["_nom"] = $LecRecup["nom"];
        $_SESSION["_prenom"] = $LecRecup["prenom"];
        $_SESSION["_annee"] = $LecRecup["anneeEleve"];
        $Statut = $LecRecup["statut"];
        
        // Si l’authentification est incorrecte, affichage d’un message d’alerte
        if($LecRecup == NULL)
        {
?>
            <script>
                alert("Saisie de l’identifiant ou du mot de passe incorrecte !\nVeuillez réessayer…");
            </script>
<?php
        }
        
        // Sinon, redirection vers l’accueil correspondant au statut de la personne qui se connecte
        else
        {
            header("Location: $Statut/accueil".ucfirst($Statut).".php");
            exit();
        }
        
        mysqli_close($BDD);
    }

?>