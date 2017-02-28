<!DOCTYPE html>

<?php
    require("../../../../BDD.php");
    mysqli_set_charset($BDD, "utf8");
    session_start();
    
    // Vérification de la connexion à Amélia
    if(!isset($_SESSION["_nom"]))
    {
        header("location: ../../../../connexion.php");
    }
    
    // Récupération des identifiants et des noms du groupe, du projet et du module auquel appartient le groupe
    if(isset($_GET["groupe"]))
    {
        $ReqGrp = "SELECT * FROM APPARTIENT, GROUPE, PROJET, MODULE WHERE anneeModule LIKE '%".$_SESSION["_annee"]."%' AND idModule = idModulePere AND idProjet = idProjetGr AND idGroupe = ".$_GET["groupe"]." AND idGroupe = idGroupeAp AND loginEleveAp = '".$_COOKIE["_idf"]."' AND admin <> 0";
        $TabGrp = mysqli_query($BDD,$ReqGrp);
        $LecGrp = mysqli_fetch_array($TabGrp);
        mysqli_free_result($TabGrp);
        
        $idGroupe = $LecGrp["idGroupe"];
        $nomGroupe = $LecGrp["nomGroupe"];
        $idProjet = $LecGrp["idProjet"];
        $nomProjet = $LecGrp["nomProjet"];
        $idModule = $LecGrp["idModule"];
        $nomModule = $LecGrp["nomModule"];
    }
    
    // Vérification de l’URL
    if(!isset($_GET["groupe"]) || $LecGrp == NULL)
    {
?>
        <script>
            window.location.href = "../../../accueilEleve.php";
        </script>
<?php
    }
?>

<html>
    
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="../../../../stylesheet.css" />
        <link rel="shortcut icon" href="../../../../images/amelia.ico" />
        <title>AMÉLIA – <?php echo $nomGroupe; ?></title>
    </head>
    
    <body>
        
        <header>
            <table class="header">
                <tr>
                    <td><h1>AMÉLIA</h1></td>
                    <td class="right">
                        <p><?php echo $_SESSION["_prenom"]." ".$_SESSION["_nom"]; ?> <a href="../../../../deconnexion.php" class="orange">Déconnexion</a><p>
                        <p><a href="../../../../compte.php" class="jaune">Mon compte</a></p>
                    </td>
                </tr>
            </table>
        </header>
        
        <table class="header">
            <tr class="header">
                <td class="header"><a href="../../../accueilEleve.php">Accueil</a> > <a href="../../module.php?module=<?php echo $idModule; ?>"><?php echo $nomModule; ?></a> > <a href="../projet.php?projet=<?php echo $idProjet; ?>"><?php echo $nomProjet; ?></a> > <?php echo $nomGroupe; ?></td>
                <td class="right"><a href="../projet.php?projet=<?php echo $idProjet; ?>" class="blanc">Retour</a></td>
            </tr>
        </table>
        
        <section>
<?php
    // Recherche de l’état du groupe (non-validé = 0, en demande = 1, validé = 2)
    $ReqValid = "SELECT * FROM GROUPE WHERE idGroupe = ".$idGroupe;
    $TabValid = mysqli_query($BDD,$ReqValid);
    $LecValid= mysqli_fetch_array($TabValid);
    mysqli_free_result($TabValid);
    
    // Si le groupe est non-validé, on peut le valider. Si le groupe est refusé, on peut le revalider.
    if($LecValid["validation"] == 0)
    {
?>            
            <h2>Validation ou suppression du groupe</h2>
            
            <article>
                <p>Lorsque votre groupe est au complet, vous devez envoyer une demande de validation à l’enseignant responsable du projet.</p>
                <p>Pour cela, veuillez cliquer sur le bouton suivant :</p>
                <form method="POST" class="right"><input type="submit" name="_valider" value="Demande de validation" class="vert" /></form>
                <p>Vous pouvez également supprimer votre groupe en cliquant sur le bouton suivant :</p>
                <p class="right"><a href="gererGroupe/supprimerGroupe.php?groupe=<?php echo $idGroupe; ?>" class="orange">Supprimer le groupe</a></p>
            </article>
<?php
    }
?>
            
            <h2>Gestion des membres du groupe</h2>
            
            <article>
<?php
    // Recherche de tous les membres du groupe avec leur nom et prénom
    $ReqMbr = "SELECT * FROM CONNEXION, APPARTIENT WHERE login = loginEleveAp AND idGroupeAp = ".$idGroupe;
    $TabMbr = mysqli_query($BDD,$ReqMbr);
    
    while($LecMbr = mysqli_fetch_array($TabMbr))
    {
        if($LecMbr["admin"] == 1)
        {
            $Statut = " (administrateur)";
        }
        
        elseif($LecMbr["admin"] == 2)
        {
            $Statut = " (propriétaire)";
        }
        
        else 
        {
            $Statut = "";
        }
?>
                <p><?php echo $LecMbr["prenom"]." ".$LecMbr["nom"].$Statut; ?></p>
<?php
        // Condition qui permet de ne pas modifier ses propres droits, ni ceux du propriétaire
        if($LecMbr["login"] != $_COOKIE["_idf"] && $Statut != " (propriétaire)")
        {
?>
                <p class="right">
<?php
            // Condition qui permet au propriétaire (uniquement) de passer la propriété a quelqu’un d’autre
            $ReqPro = "SELECT * FROM APPARTIENT WHERE loginEleveAp = '".$_COOKIE["_idf"]."' AND admin = 2 AND idGroupeAp = ".$idGroupe;
            $TabPro = mysqli_query($BDD,$ReqPro);
            $LecPro = mysqli_fetch_array($TabPro);
            mysqli_free_result($TabPro);
            
            if($LecPro != NULL)
            {
?>
                    <a href="gererGroupe/modifierMembre.php?admin=2&groupe=<?php echo $idGroupe; ?>&membre=<?php echo $LecMbr["login"]; ?>" class="bleu">Propriétaire</a>
<?php
            }
?>
                    <a href="gererGroupe/modifierMembre.php?admin=1&groupe=<?php echo $idGroupe; ?>&membre=<?php echo $LecMbr["login"]; ?>" class="jaune">Administrateur</a>
<?php
            // Condition qui empêche de supprimer des membres lorsque le groupe est validé ou en demande de validation
            if($LecValid["validation"] == 0)
            {
?>
                    <a href="gererGroupe/supprimerMembre.php?groupe=<?php echo $idGroupe; ?>&membre=<?php echo $LecMbr["login"]; ?>" class="orange">Supprimer</a>
<?php
            }
?>
                </p>
<?php
        }
    }
    
    mysqli_free_result($TabMbr);
    
    if($LecValid["validation"] == 0)
    {
?>
                <p class="right"><a href="gererGroupe/ajouterMembre.php?groupe=<?php echo $idGroupe; ?>" class="vert">Ajouter des membres</a></p>
<?php
    }
?>
            </article>
            
<?php
    
    // Vérification si le projet auquel appartient le groupe autorise les candidatures
    $ReqType = "SELECT * FROM PROJET, GROUPE WHERE candidature <> 0 AND idProjetGr = idProjet AND idGroupe = ".$idGroupe;
    $TabType = mysqli_query($BDD,$ReqType);
    $LecType = mysqli_fetch_array($TabType);
    mysqli_free_result($TabType);
    
    // Vérification s’il y a des candidatures dans le groupe
    $ReqVide = "SELECT * FROM CONNEXION, CANDIDATURE WHERE login = loginEleveCa AND idGroupeCa = ".$idGroupe;
    $TabVide = mysqli_query($BDD,$ReqVide);
    $LecVide = mysqli_fetch_array($TabVide);
    mysqli_free_result($TabVide);
    
    // Si le projet autorise les candidatures et qu’il y a des candidatures, on les affiche
    if($LecType != NULL && $LecVide != NULL && $LecValid["validation"] == 0)
    {
?>
            <h2>Gestion des candidatures dans le groupe</h2>
            
            <h3>Candidatures sans réponse</h3>
            
            <article>
<?php
        // Recherche de toutes la candidatures sans réponse
        $ReqCdt = "SELECT * FROM CONNEXION, CANDIDATURE WHERE login = loginEleveCa AND etat = 0 AND idGroupeCa = ".$idGroupe;
        $TabCdt = mysqli_query($BDD,$ReqCdt);
        
        // Vérification pour savoir s’il y a des candidatures sans réponse
        if(mysqli_num_rows($TabCdt) != 0)
        {
            while($LecCdt = mysqli_fetch_array($TabCdt))
            {
?>
                <p><?php echo $LecCdt["prenom"]." ".$LecCdt["nom"]." (".$LecCdt["anneeEleve"].")"; ?></p>
                <p class="right">
                    <a href="gererGroupe/voirCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="bleu">Voir</a>
                    <a href="gererGroupe/accepterCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="vert">Accepter</a>
                    <a href="gererGroupe/refuserCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="orange">Refuser</a>
                </p>
<?php
            }
        }
        
        else 
        {
?>
                <p>Aucune candidature sans réponse…</p>
<?php
        }
?>
            </article>
            
            <h3>Candidatures acceptées</h3>
            
            <article>
<?php
        // Recherche de toutes les candidatures acceptées
        $ReqCdt = "SELECT * FROM CONNEXION, CANDIDATURE WHERE login = loginEleveCa AND etat = 1 AND idGroupeCa = ".$idGroupe;
        $TabCdt = mysqli_query($BDD,$ReqCdt);
        
        
        // Vérification pour savoir s’il y a des candidatures acceptées
        if(mysqli_num_rows($TabCdt) != 0)
        {
            while($LecCdt = mysqli_fetch_array($TabCdt))
            {
?>
                <p><?php echo $LecCdt["prenom"]." ".$LecCdt["nom"]." (".$LecCdt["anneeEleve"].")"; ?></p>
                <p class="right">
                    <a href="gererGroupe/voirCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="bleu">Voir</a>
                </p>
<?php
            }
        }
        
        else 
        {
?>
                <p>Aucune candidature validée…</p>
<?php
        }
?>
            </article>
            
            <h3>Candidatures refusées</h3>
            
            <article>
<?php
        // Recherche de toutes les candidatures refusées
        $ReqCdt = "SELECT * FROM CONNEXION, CANDIDATURE WHERE login = loginEleveCa AND etat = 2 AND idGroupeCa = ".$idGroupe;
        $TabCdt = mysqli_query($BDD,$ReqCdt);
        
        // Vérification pour savoir s’il y a des candidatures refusées
        if(mysqli_num_rows($TabCdt) != 0)
        {
            while($LecCdt = mysqli_fetch_array($TabCdt))
            {
?>
                <p><?php echo $LecCdt["prenom"]." ".$LecCdt["nom"]." (".$LecCdt["anneeEleve"].")"; ?></p>
                <p class="right">
                    <a href="gererGroupe/voirCandidature.php?candid=<?php echo $LecCdt["idCandidature"]; ?>" class="bleu">Voir</a>
                </p>
<?php
            }
        }
        
        else 
        {
?>
                <p>Aucune candidature refusée…</p>
<?php
        }
        
        mysqli_free_result($TabCdt);
?>
            </article>
            
<?php
    }
?>
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php
    if(isset($_POST["_valider"]))
    {
        $ReqValid = "UPDATE GROUPE SET validation = 1 WHERE idGroupe = ".$idGroupe;
        
        $ReqCan = "SELECT * FROM CANDIDATURE WHERE idGroupeCa = ".$idGroupe;
        $TabCan = mysqli_query($BDD,$ReqCan);
        
        while($LecCan = mysqli_fetch_array($TabCan))
        {
            $ReqRef = "UPDATE CANDIDATURE SET etat = 2 WHERE idCandidature = ".$LecCan["idCandidature"];
            mysqli_query($BDD,$ReqRef);
        }
        
        if(mysqli_query($BDD,$ReqValid))
        {
?>
                <script>
                    alert("Votre demande a bien été envoyée !");
                    window.location.href = "gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
                </script>
<?php
        }
        
        else 
        {
?>
                <script>
                    alert("Erreur ! Votre demande n’a pas pu être envoyée.\nVeuillez réessayer.");
                    window.location.href = "gererGroupe.php?groupe=<?php echo $idGroupe; ?>";
                </script>
<?php
        }
    }
    
    mysqli_close($BDD);
?>