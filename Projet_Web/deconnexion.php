<?php
    // On démarre la session
    session_start();

    // On détruit les variables de la session
    session_unset();

    // On détruit la session
    session_destroy();

    // On retourne sur la page de connexion
    header("location: connexion.php");
?>