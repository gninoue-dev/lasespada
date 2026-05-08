<?php
// Exemple futur avec base de données
// require "config/database.php";

// Récupération de l'ID du sinistre
$id = $_GET['id'] ?? null;

// Exemple de données simulées (à remplacer par SQL plus tard)
$sinistre = [
    "id" => $id,
    "client" => "client2",
    "type" => "Accident automobile",
    "montant" => "450 000 F",
    "statut" => "En attente",
    "date" => "07/05/2026",
    "description" => "Collision avec un autre véhicule à un carrefour.",
];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Détail sinistre</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="stylesheet" href="../assets/css/detail_sinistres.css">
<script src="../assets/js/detail_sinistres.js" defer></script>


</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <div style="display:flex;gap:10px;align-items:center">

            <button class="back" onclick="history.back()">
                ← Retour
            </button>

            <h2 style="margin:0">
                Détail sinistre #<?= htmlspecialchars($sinistre["id"]) ?>
            </h2>

        </div>

    </div>

    <!-- INFOS SINISTRE -->
    <div class="card">

        <h3>Informations du sinistre</h3>

        <div class="grid">

            <div>
                <div class="label">Client</div>
                <div class="value"><?= $sinistre["client"] ?></div>
            </div>

            <div>
                <div class="label">Type</div>
                <div class="value"><?= $sinistre["type"] ?></div>
            </div>

            <div>
                <div class="label">Montant demandé</div>
                <div class="value"><?= $sinistre["montant"] ?></div>
            </div>

            <div>
                <div class="label">Date</div>
                <div class="value"><?= $sinistre["date"] ?></div>
            </div>

            <div>
                <div class="label">Statut</div>
                <div class="value">
                    <span class="badge pending">
                        <?= $sinistre["statut"] ?>
                    </span>
                </div>
            </div>

        </div>

    </div>

    <!-- DESCRIPTION -->
    <div class="card">

        <h3>Description</h3>

        <p style="color:#4b5563">
            <?= $sinistre["description"] ?>
        </p>

    </div>

    <!-- PIECES JOINTES -->
    <div class="card">

        <h3>Pièces jointes</h3>

        <div style="margin-top:10px;color:#6b7280">
            📄 Aucun fichier uploadé (à connecter plus tard)
        </div>

    </div>

    <!-- ACTIONS ADMIN -->
    <div class="card">

        <h3>Actions administrateur</h3>

        <p style="color:#6b7280;margin-bottom:15px">
            Valider ou refuser ce sinistre
        </p>

        <textarea placeholder="Ajouter une remarque..."></textarea>

        <div class="actions" style="margin-top:15px">

            <button class="btn btn-validate">
                ✔ Valider
            </button>

            <button class="btn btn-refuse">
                ✖ Refuser
            </button>

            <button class="btn btn-warn">
                ⚠ Mettre en attente
            </button>

        </div>

    </div>

</div>



</body>
</html>