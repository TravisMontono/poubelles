<?php
session_start();
session_destroy();
header("Location: index.php"); // Redirigez l'utilisateur vers la page d'accueil ou une autre page de connexion
exit();
?>