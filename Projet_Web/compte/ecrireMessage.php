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
        <title>AMÉLIA – Écrire un message</title>
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
                <td><a href="../compte.php">Mon compte</a> > Écrire un message</td>
                <td class="right"><a href="../compte.php" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
            
            <h2>Écrire un message</h2>
            
            <article>
                <form method="POST">
                    <table>
                        <tr>
                            <td>Choisir le destinataire :</td>
                            <td>
                                <select name="_desti">
<?php
    // Récupération de l’ensemble des personnes et affichage dans le liste déroulante
    $ReqPrs = "SELECT * FROM CONNEXION WHERE login <> '".$_COOKIE["_idf"]."'";
    $TabPrs = mysqli_query($BDD,$ReqPrs);
    
    while($LecPrs = mysqli_fetch_array($TabPrs))
    {
        // Détermination du statut
        if($LecPrs["statut"] == "eleve") { $Statut = "Élève"; }
        elseif($LecPrs["statut"] == "enseignant") { $Statut = "Enseignant"; }
        elseif($LecPrs["statut"] == "client") { $Statut = "Client"; }
        elseif($LecPrs["statut"] == "gestion") { $Statut = "Gestionnaire"; }
?>
                                    <option value="<?php echo $LecPrs["login"]; ?>"><?php echo $LecPrs["prenom"]." ".$LecPrs["nom"]." (".$Statut.")"; ?></option>
<?php
    }
    
    mysqli_free_result($TabPrs);
?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Entrer le sujet du message :</td>
                            <td><input type="text" name="_sujet" required /></td>
                        </tr>
                        <tr>
                            <td>Saisir le texte de votre message :</td>
                            <td></td>
                        </tr>
                    </table>
                    <textarea name="_message" rows=5></textarea>
                    <p class="right"><input type="submit" name="_envoyer" value="Envoyer" class="vert" /></p>
                </form>
            </article>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_envoyer"]))
    {
        // Gestion des apostrophes
        $message = $_POST["_message"];
        $message = str_replace("'","’",$message);
        
        $sujet = $_POST["_sujet"];
        $sujet = str_replace("'","’",$sujet);
        
        // Envoi du message
        $ReqEnv = "INSERT INTO MESSAGE (loginEnvoi, loginRecoi, sujet, message) VALUES ('".$_COOKIE["_idf"]."', '".$_POST["_desti"]."', '".$sujet."', '".$message."')";
        
        // Cas normal
        if(mysqli_query($BDD,$ReqEnv))
        {
?>
            <script>
                alert("Votre message a bien été envoyé !");
                window.location.href = "../compte.php";
            </script>
<?php
        }
        
        // Cas dégénéré
        else 
        {
?>
            <script>
                alert("Erreur ! Votre message n’a pas pu être envoyé.\nVeuillez réessayer.");
                window.location.href = "ecrireMessage.php";
            </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>