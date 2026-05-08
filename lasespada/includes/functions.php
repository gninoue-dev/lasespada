<?php
// ══════════════════════════════════════════════════════
//  includes/functions.php — Fonctions utilitaires
// ══════════════════════════════════════════════════════

// Retourne le badge HTML selon le statut
function badgeStatut(string $statut): string {
    return match($statut) {
        'validé'      => '<span style="color:#16a34a;font-weight:bold">Validé</span>',
        'rejeté'      => '<span style="color:#dc2626;font-weight:bold">Refusé</span>',
        'en_attente'  => '<span style="color:#d97706;font-weight:bold">En attente</span>',
        default       => '<span>' . htmlspecialchars($statut) . '</span>'
    };
}

// Retourne le badge HTML selon le niveau de fraude
function badgeNiveau(string $niveau): string {
    return match($niveau) {
        'normal'          => '<span style="color:#16a34a">Normal</span>',
        'douteux'         => '<span style="color:#d97706">Douteux</span>',
        'frauduleux'      => '<span style="color:#dc2626;font-weight:bold">Frauduleux</span>',
        'fraude probable' => '<span style="color:#dc2626;font-weight:bold">Fraude probable</span>',
        default           => '<span>' . htmlspecialchars($niveau) . '</span>'
    };
}

// Formate un montant
function formatMontant(float $montant): string {
    return number_format($montant, 0, ',', ' ') . ' FCFA';
}

// Nettoie une entrée utilisateur
function clean(string $val): string {
    return htmlspecialchars(trim($val));
}

// Retourne l'IP réelle
function getIP(): string {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}