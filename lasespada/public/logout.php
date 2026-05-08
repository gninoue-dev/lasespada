<?php
//  public/logout.php — Déconnexion de l'utilisateur
session_start();

// Supprime toutes les variables de session
session_unset();

// Détruit la session côté serveur
session_destroy();

// Redirige vers la page de connexion
header('Location: connexion.php');
exit;