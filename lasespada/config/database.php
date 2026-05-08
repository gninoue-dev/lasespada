<?php
// ══════════════════════════════════════════════════════
//  config/database.php — Connexion PDO
// ══════════════════════════════════════════════════════

try {
    $pdo = new PDO("mysql:host=localhost;dbname=fraude_assurance;charset=utf8mb4", "root", "");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode([
        "statut" => "erreur",
        "message" => "Connexion échouée : " . $e->getMessage()
    ]));
}
?>