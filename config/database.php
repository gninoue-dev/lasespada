<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=fraude_assurance;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}
?>
