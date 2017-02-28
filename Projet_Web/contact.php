<!DOCTYPE html>

<?php
    require("BDD.php");
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
        <title>AMÉLIA – Contact</title>
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
                <td></td>
                <td class="right"><a href="<?php echo $Statut; ?>/accueil<?php echo ucfirst($Statut); ?>.php" class="blanc">Retour à l’accueil</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Contact</h2>
            
            <form method="POST">
                
                <article>
                    <table>
                        <tr>
                            <td>Votre adresse mél :</td>
                            <td><input type="email" name="_mail" required /></td>
                        </tr>
                        <tr>
                            <td>Votre sujet :</td>
                            <td><input type="text" name="_sujet" required /></td>
                        </tr>
                        <tr>
                            <td>Votre message :</td>
                        </tr>
                    </table>
                    
                    <p><textarea name="_message" cols=86 rows=5 required></textarea></p>
                </article>
                
                <p class="right"><input type="submit" name="_envoyer" value="Envoyer" class="vert" /></p>
                
            </form>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - Contact</p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_envoyer"]))
    {
        // Envoi du message
        $to = "amelia@ensc.fr";
        $subject = $_POST["_sujet"];
        $message = $_POST["_message"];
        $header  = "MIME-Version: 1.0\r\n";
        $header  .= 'From: '.$_POST["_mail"].'\r\n';
        $header  .= 'Content-Type: text/plain; charset="iso-8859-1"';
        $header  .= '\r\nContent-Transfer-Encoding: 8bit\r\n';
        $header  .= 'X-Mailer:PHP/'. phpversion()."\r\n";
        
        // Si le message a été envoyé, on l’affiche
        if(mail($to,$subject,$message,$header))
        {
?>
            <script>
                alert("Votre demande a été enregistrée avec succès !");
            </script>
<?php
        }
        
        // Sinon, on renvoie un message d’erreur
        else 
        {
?>
            <script>
                alert("Erreur ! Votre demande n’a pas pu être envoyée.\nVeuillez réessayer.");
            </script>
<?php
        }
    }
?>