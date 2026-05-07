<?php
// includes/functions.php

// Génère un token CSRF et le stocke en session
function genererCSRF(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifie le token CSRF soumis
function verifierCSRF(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Nettoie une entrée utilisateur
function clean(string $val): string
{
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}

// Retourne le badge HTML selon le statut
function badgeStatut(string $statut): string
{
    return match ($statut) {
        'normal' => '<span class="badge normal">Normal</span>',
        'douteux' => '<span class="badge douteux">Douteux</span>',
        'frauduleux' => '<span class="badge frauduleux">Frauduleux</span>',
        default => '<span class="badge">Inconnu</span>'
    };
}

// Retourne le statut selon le score
function statutParScore(int $score): string
{
    if ($score >= 80)
        return 'frauduleux';
    if ($score >= 40)
        return 'douteux';
    return 'normal';
}