<!DOCTYPE html>

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
        <meta charset="utf-8" />
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
            
<?php
    // Récupération du groupe auquel appartient l’élève dans le projet sélectionné
    $ReqGrp = "SELECT * FROM APPARTIENT, CONNEXION, GROUPE WHERE login = '".$_COOKIE["_idf"]."' AND login = loginEleveAp AND idGroupeAp = idGroupe AND idProjetGr = ".$idProjet." GROUP BY idGroupe";
    $TabGrp = mysqli_query($BDD,$ReqGrp);
    
    // Vérification pour savoir si l’élève appartiant à un groupe
    if(mysqli_num_rows($TabGrp) != 0)
    {
        // Si oui, on l’affiche
        while($LecGrp = mysqli_fetch_array($TabGrp))
        {
            // Récupération des noms et prénoms des élèves du groupe
            $ReqElv = "SELECT * FROM CONNEXION, APPARTIENT, GROUPE WHERE login = loginEleveAp AND idGroupeAp = idGroupe AND idGroupe = ".$LecGrp["idGroupe"];
            $TabElv = mysqli_query($BDD,$ReqElv);
            
            // Récupération des noms et des liens permettant d’accéder aux documents remis
            $ReqDoc = "SELECT nomRendu, urlRendu FROM RENDU, GROUPE WHERE idGroupeRe = idGroupe AND idGroupe = ".$LecGrp["idGroupe"];
            $TabDoc = mysqli_query($BDD,$ReqDoc);
            
            // Mise en forme de la description
            $Dcr = $LecGrp["description"];
            $Dcr = str_replace(' "',' “',$Dcr);
            $Dcr = str_replace('"','”',$Dcr);
?>
            <h2><?php echo $LecGrp["nomGroupe"]; ?></h2>
            
<?php
            // Récupération du statut de l’élève dans le projet (propriétaire [2], administrateur [1], rien [0])
            $ReqAdmin = "SELECT * FROM APPARTIENT WHERE admin <> 0 AND loginEleveAp = '".$_COOKIE["_idf"]."' AND idGroupeAp = ".$LecGrp["idGroupe"];
            $TabAdmin = mysqli_query($BDD,$ReqAdmin);
            $LecAdmin = mysqli_fetch_array($TabAdmin);
            mysqli_free_result($TabAdmin);
            
            // Si l’élève est propriétaire ou administrateur, il peut modifier les caractéristiques du groupe et le gérer
            if($LecAdmin["admin"] != 0)
            {
?>
            <p class="right">
                <a href="projet/modifierGroupe.php?groupe=<?php echo $LecGrp["idGroupe"]; ?>" class="jaune">Modifier le nom ou la description du groupe</a>
                <a href="projet/gererGroupe.php?groupe=<?php echo $LecGrp["idGroupe"]; ?>" class="bleu">Gérer le groupe</a><br/>
            </p>
<?php
            }
            
            // Si l’élève est propriétaire ou administrateur, et que le groupe a été validé par l’enseignant responsable, il peut ajouter ou supprimer des documents
            if($LecAdmin["admin"] != 0 && $LecGrp["validation"] == 2)
            {
?>
            <p class="right">
                <a href="projet/ajouterFichier.php?groupe=<?php echo $LecGrp["idGroupe"]; ?>" class="vert">Ajouter un document</a>
                <a href="projet/supprimerFichier.php?groupe=<?php echo $LecGrp["idGroupe"]; ?>" class="orange">Supprimer des documents</a>
            </p>
<?php
            }
?>
            
            <article>
                <table class="large">
<?php
            // Affichage éventuel de la description du groupe
            if($LecGrp["description"] != NULL || $LecGrp["description"] != "")
            {
?>
                    <tr class="projet">
                        <td class="projet">Description :</td>
                        <td><?php echo $Dcr; ?></td>
                    </tr>
<?php
            }
?>
                    <tr class="projet">
                        <td>Élèves :</td>
                        <td><?php
            while($LecElv = mysqli_fetch_array($TabElv))
            {
                if($LecElv["admin"] == 1)
                {
                    $Statut = " (Administrateur)";
                }
                
                elseif($LecElv["admin"] == 2)
                {
                    $Statut = " (Propriétaire)";
                }
                
                else 
                {
                    $Statut = "";
                }
                
                echo $LecElv["prenom"]." ".$LecElv["nom"].$Statut; ?><br/><?php
            } ?></td>
                    </tr>
                    <tr class="projet">
                        <td>Documents remis :</td>
                        <td><?php
            if(mysqli_num_rows($TabDoc) != 0)
            {
                while($LecDoc = mysqli_fetch_array($TabDoc))
                { ?><a href="../../../rendu/<?php echo $LecDoc["urlRendu"]?>"><?php echo $LecDoc["nomRendu"]; ?></a><br/><?php }
            }
            
            else { ?>Aucun document remis…<?php } ?></td>
                    </tr>
                </table>
            </article>
            
<?php
            mysqli_free_result($TabDoc);
            mysqli_free_result($TabElv);
        }
    }
    
    else 
    {
        // Vérification si le projet autorise ou non les candidatures et si des groupes ont été créés
        $ReqType = "SELECT * FROM PROJET, GROUPE WHERE candidature <> 0 AND validation = 0 AND idProjetGr = idProjet AND idProjet = ".$idProjet;
        $TabType = mysqli_query($BDD,$ReqType);
        $LecType = mysqli_fetch_array($TabType);
        mysqli_free_result($TabType);
        
        // Si le projet autorise les candidatures et qu’il y a des groupes déjà existants
        if($LecType != NULL) 
        {
            // Vérification si le projet autorise ou non les élèves à creer un groupe
            $ReqCree = "SELECT * FROM PROJET WHERE creerGp LIKE '%".$_SESSION["_annee"]."%' AND idProjet = ".$idProjet;
            $TabCree = mysqli_query($BDD,$ReqCree);
            $LecCree = mysqli_fetch_array($TabCree);
            mysqli_free_result($TabCree);
            
            // Cas où le projet autorise l’élève a créer un groupe
            if($LecCree != NULL)
            {
?>
            <h2><?php echo $nomProjet; ?></h2>
            
            <article>
                <p>Vous n’êtes actuellement inscrit dans aucun groupe de ce projet.</p>
                <p>Pour y remédier, choisissez l’une des deux options suivantes :</p>
                <ul>
                    <li><a href="creerGroupe.php?projet=<?php echo $idProjet; ?>">Créer un nouveau groupe</a></li>
                    <li><a href="../ajouterCandidature.php">Candidater dans un groupe déjà existant</a></li>
                </ul>
<?php
            }
            
            // cas où l’élève ne peut que candidater
            else 
            {
?>
            <h2><?php echo $nomProjet; ?></h2>
            
            <article>
                <p>Vous n’êtes actuellement inscrit dans aucun groupe de ce projet.</p>
                <p>Pour y remédier : <a href="../ajouterCandidature.php">Candidater dans un groupe déjà existant</a></p>
<?php
            }
        }
        
        // Sinon, si le projet n’autorise pas les candidatures et/ou il n’existe aucun groupe dans lequel candidater
        else 
        {
            // Vérification si le projet autorise ou non les élèves à creer un groupe
            $ReqCree = "SELECT * FROM PROJET WHERE creerGp LIKE '%".$_SESSION["_annee"]."%' AND idProjet = ".$idProjet;
            $TabCree = mysqli_query($BDD,$ReqCree);
            $LecCree = mysqli_fetch_array($TabCree);
            mysqli_free_result($TabCree);
            
            // Cas où le projet autorise l’élève a créer un groupe
            if($LecCree != NULL)
            {
?>
            <h2><?php echo $nomProjet; ?></h2>
            
            <article>
                <p>Vous n’êtes actuellement inscrit dans aucun groupe de ce projet.</p>
                <p>
                    Pour y remédier : <a href="creerGroupe.php?projet=<?php echo $idProjet; ?>">Créer un nouveau groupe</a>
                </p>
<?php
            }
            
            // Cas où l’élève ne peut que candidater
            else 
            {
?>
            <h2><?php echo $nomProjet; ?></h2>
            
            <article>
                <p>Vous n’êtes actuellement inscrit dans aucun groupe de ce projet.</p>
                <p>Pour y remédier : Candidater dans un groupe dès que l’opportunité se présentera.</a></p>
<?php
            }
        }
        
        // Récupération et affichage des groupes déjà existants dans le projet
        $ReqExi = "SELECT * FROM GROUPE WHERE idProjetGr = ".$idProjet;
        $TabExi = mysqli_query($BDD,$ReqExi);
        
        // S’il existe déjà des groupes, on les affiche
        if(mysqli_num_rows($TabExi) != 0)
        {
?>
                <p>Pour information, voici la liste des groupes déjà existants :</p>
                <ul>
<?php
        
                while($LecExi = mysqli_fetch_array($TabExi))
                {
?>
                    <li><?php echo $LecExi["nomGroupe"]; ?> (<?php
                    // Récupération des noms et prénoms des élèves appartenant au groupe affiché
                    $ReqApp = "SELECT * FROM APPARTIENT, CONNEXION WHERE login = loginEleveAp AND idGroupeAp = ".$LecExi["idGroupe"];
                    $TabApp = mysqli_query($BDD,$ReqApp);
                    
                    $i = 0;
                    
                    // Affichage des noms et prénoms
                    while($LecApp = mysqli_fetch_array($TabApp))
                    {
                        if($i != 0)
                        {
                            echo ", ";
                        }
                        
                        echo $LecApp["prenom"]." ".$LecApp["nom"];
                        
                        $i += 1;
                    }
                    
                    mysqli_free_result($TabApp);
?>)</li>
<?php
                }
?>
                </ul>
            </article>
<?php
        }
            
        // Sinon, on affiche qu’il n’y a aucun groupe
        else 
        {
?>
                <p>Aucun groupe n’a été créé dans ce projet…</p>
            </article>
<?php
        }
    
        mysqli_free_result($TabExi);
    }
    
    mysqli_free_result($TabGrp);
?>
            
        </section>
        
        <footer>
            <p>© Copyright 2016 Amélia - <a href="../../../contact.php">Contact</a></p>
        </footer>
        
    </body>
    
</html>

<?php    
    mysqli_close($BDD);
?>