<!DOCTYPE html>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="stylesheet.css" />
        <link rel="shortcut icon" href="images/amelia.ico" />
        <title>AMÉLIA – Mot de passe oublié</title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right"><img height=100px src="images/ensc.png" alt="Logo de l’ENSC" title="Logo de l’ENSC" /></td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr>
                <td></td>
                <td class="right"></td>
            </tr>
        </table>

        <section class="connect">
            
            <h2 class="connect">Mot de passe oublié</h2>
            
            <form method="POST">
                
                <table class="connect">
                    <tr>
                        <td>Identifiant :</td>
                        <td><input type="text" name="_idf" required/></td>
                    </tr>
                    <tr>
                        <td>Adresse mél :</td>
                        <td><input type="email" name="_mail" required/></td>
                    </tr>
                    <tr>
                        <td><a href="connexion.php" class="jaune">Retour</a></td>
                        <td class="right"><input type="submit" name="_envoyer" value="Envoyer" class="vert" /></td>
                    </tr>
                </table>
                
            </form>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia – <a href="contactConnexion.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_envoyer"]))
    {
        require ("BDD.php");
        
        // Récupération du mot de passe associé au login
        $ReqRecup = "SELECT mdp FROM CONNEXION WHERE login = '".$_POST["_idf"]."'";
        $TabRecup = mysqli_query($BDD, $ReqRecup);
        $LecRecup = mysqli_fetch_array($TabRecup)["mdp"];
        
        // Si le login n’existe pas, on renvoie un message d’erreur
        if($LecRecup == NULL)
        {
?>
            <script>
                alert("Saisie de l’identifiant incorrecte !\nVeuillez réessayer…");
            </script>
<?php
        }
        
        // Sinon, on n’envoie le mot de passe associé à l’adresse mél indiquée par l’utilisateur
        else
        {
            $to = $_POST["_mail"];
            $subject = "AMÉLIA – Mot de passe oublié";
            $message = "Voici votre mot de passe pour pouvoir accéder au site AMÉLIA : ".$LecRecup;
            $header  = "MIME-Version: 1.0\r\n";
            $header  .= "From: amelia@ensc.fr\r\n";
            $header  .= 'Content-Type: text/plain; charset="iso-8859-1"';
            $header  .= "\r\nContent-Transfer-Encoding: 8bit\r\n";
            $header  .= 'X-Mailer:PHP/'. phpversion()."\r\n";
            
            // Si l’envoi du mél est effectué, on affiche le succès de l’envoi
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
                    alert("Votre demande n’a pas pu être envoyée.\nVeuillez réessayer.");
                </script>
<?php
            }
        }
        
        mysqli_free_result($TabRecup);
        
        mysqli_close($BDD);
    }
?>