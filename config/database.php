<?php
// config/database.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'fraude_assurance');
define('DB_USER', 'root');
define('DB_PASS', '');         // vide par défaut sur XAMPP
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // erreurs visibles
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // tableau associatif
            PDO::ATTR_EMULATE_PREPARES => false,                    // vraies requêtes préparées
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En prod : logger l'erreur, ne pas l'afficher
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    return $pdo;
}