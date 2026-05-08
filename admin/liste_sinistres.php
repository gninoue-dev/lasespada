<?php
// (Plus tard ici tu connecteras ta base de données)
// require "config/database.php";
// $sinistres = $pdo->query("SELECT * FROM sinistres")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Liste des sinistres</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="stylesheet" href="../assets/css/liste_sinistres.css">
<script src="../assets/js/liste_sincères.js" defer></script>



</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <h1>Liste des sinistres</h1>

        <div style="display:flex;gap:10px;align-items:center">

            <div class="search-box">
                <i class="ti ti-search"></i>
                <input type="text" id="search" placeholder="Rechercher client...">
            </div>

            <button class="btn">
                + Nouveau
            </button>

        </div>

    </div>

    <!-- STATS -->
    <div class="stats">

        <div class="card">
            <div>Total</div>
            <div class="value">128</div>
        </div>

        <div class="card">
            <div>En attente</div>
            <div class="value" style="color:#d97706">23</div>
        </div>

        <div class="card">
            <div>Validés</div>
            <div class="value" style="color:#16a34a">89</div>
        </div>

        <div class="card">
            <div>Refusés</div>
            <div class="value" style="color:#dc2626">16</div>
        </div>

    </div>

    <!-- FILTERS -->
    <div class="filters">
        <button class="filter active" data-filter="all">Tous</button>
        <button class="filter" data-filter="pending">En attente</button>
        <button class="filter" data-filter="valid">Validés</button>
        <button class="filter" data-filter="refused">Refusés</button>
    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="table">

                <tr data-status="pending">
                    <td>#001</td>
                    <td>client2</td>
                    <td>Accident</td>
                    <td>450 000 F</td>
                    <td><span class="badge pending">En attente</span></td>
                    <td>07/05/2026</td>
                    <td>
                        <button class="view-btn" onclick="goDetail(1)">
                            Voir
                        </button>
                    </td>
                </tr>

                <tr data-status="valid">
                    <td>#002</td>
                    <td>client4</td>
                    <td>Incendie</td>
                    <td>2 500 000 F</td>
                    <td><span class="badge valid">Validé</span></td>
                    <td>05/05/2026</td>
                    <td>
                        <button class="view-btn" onclick="goDetail(2)">
                            Voir
                        </button>
                    </td>
                </tr>

                <tr data-status="refused">
                    <td>#003</td>
                    <td>client1</td>
                    <td>Vol</td>
                    <td>300 000 F</td>
                    <td><span class="badge refused">Refusé</span></td>
                    <td>02/05/2026</td>
                    <td>
                        <button class="view-btn" onclick="goDetail(3)">
                            Voir
                        </button>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>



</body>
</html>