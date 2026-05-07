<?php
// includes/session.php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,   // mettre true en HTTPS
        'httponly' => true,    // inaccessible via JS
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Régénère l'ID session après connexion (anti-fixation)
function regenererSession(): void
{
    session_regenerate_id(true);
}

// Vérifie si l'utilisateur est connecté (assuré)
function estConnecte(): bool
{
    return isset($_SESSION['utilisateur_id']);
}

// Vérifie si l'admin est connecté
function estAdmin(): bool
{
    return isset($_SESSION['admin_id']);
}

// Redirige si non connecté
function requireConnexion(): void
{
    if (!estConnecte()) {
        header('Location: /public/connexion.php');
        exit;
    }
}

// Redirige si non admin
function requireAdmin(): void
{
    if (!estAdmin()) {
        header('Location: /public/connexion.php');
        exit;
    }
}