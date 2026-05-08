<?php
// ══════════════════════════════════════════════════════
//  includes/session.php — Vérification session
// ══════════════════════════════════════════════════════

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si l'utilisateur est connecté
function isConnected(): bool
{
    return isset($_SESSION["user_id"]);
}

// Vérifie si l'utilisateur est admin (agent ou super_admin)
function isAdmin(): bool
{
    return isset($_SESSION["role"]) &&
        in_array($_SESSION["role"], ["agent", "super_admin", "admin"]);
}

// Redirige si non connecté
function requireLogin(): void
{
    if (!isConnected()) {
        header("Location: ../public/connexion.php");
        exit;
    }
}

// Redirige si non admin
function requireAdmin(): void
{
    if (!isAdmin()) {
        header("Location: ../public/connexion.php");
        exit;
    }
}