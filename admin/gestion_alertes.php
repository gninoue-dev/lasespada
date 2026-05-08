<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des alertes</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<script src="../assets/js/gestion_alertes.js" defer></script>
<link rel="stylesheet" href="../assets/css/gestion_alertes.css">


</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">

    <div class="title">Gestion des alertes</div>

    <div class="filters">
        <button class="filter-btn active" data-filter="all">Toutes</button>
        <button class="filter-btn" data-filter="critical">Critiques</button>
        <button class="filter-btn" data-filter="warning">Warnings</button>
        <button class="filter-btn" data-filter="info">Infos</button>
    </div>

</div>

<!-- STATS -->
<div class="stats">

    <div class="card">
        <div>Total alertes</div>
        <div class="value">6</div>
    </div>

    <div class="card">
        <div>Critiques</div>
        <div class="value" style="color:#dc2626">2</div>
    </div>

    <div class="card">
        <div>Non traitées</div>
        <div class="value" style="color:#f59e0b">3</div>
    </div>

</div>

<!-- ALERTES -->
<div class="alert-list" id="alertList">

    <div class="alert critical" data-type="critical" data-id="1">
        <div class="alert-text">
             Sinistre suspect - client2 - 2 500 000 F
        </div>
        <div>
            <button class="btn view" onclick="goSinistre(1)">Voir</button>
            <button class="btn done" onclick="markDone(this)">Traiter</button>
            <button class="btn ignore" onclick="removeAlert(this)">Ignorer</button>
        </div>
    </div>

    <div class="alert warning" data-type="warning" data-id="2">
        <div class="alert-text">
            ⚠ Cotisation impayée - client5
        </div>
        <div>
            <button class="btn view" onclick="goSinistre(2)">Voir</button>
            <button class="btn done" onclick="markDone(this)">Résolu</button>
            <button class="btn ignore" onclick="removeAlert(this)">Ignorer</button>
        </div>
    </div>

    <div class="alert info" data-type="info" data-id="3">
        <div class="alert-text">
             Nouveau sinistre déclaré - client1
        </div>
        <div>
            <button class="btn view" onclick="goSinistre(3)">Voir</button>
            <button class="btn ignore" onclick="removeAlert(this)">Ignorer</button>
        </div>
    </div>

</div>

</div>



</body>
</html>