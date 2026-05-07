<?php
// public/historique.php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireConnexion();

$pdo = getDB();
$utilisateur_id = $_SESSION['utilisateur_id'];

// Infos utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$utilisateur_id]);
$user = $stmt->fetch();

// Liste sinistres
$stmt2 = $pdo->prepare("
    SELECT s.*, 
           (SELECT COUNT(*) FROM preuves p WHERE p.sinistre_id = s.id) AS nb_preuves
    FROM sinistres s
    WHERE s.utilisateur_id = ?
    ORDER BY s.date_declaration DESC
");
$stmt2->execute([$utilisateur_id]);
$sinistres = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes sinistres — FraudShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .user-info {
            background: #fff;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .06);
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #1a56db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-meta h3 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .user-meta p {
            font-size: .85rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .score-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: .85rem;
        }

        .score-pill.normal {
            background: #def7ec;
            color: #057a55;
        }

        .score-pill.douteux {
            background: #fff3cd;
            color: #856404;
        }

        .score-pill.frauduleux {
            background: #fde8e8;
            color: #e02424;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state span {
            font-size: 3rem;
            display: block;
            margin-bottom: 12px;
        }

        .sinistre-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .06);
            border-left: 4px solid #e5e7eb;
            transition: border-color .2s;
        }

        .sinistre-card.normal {
            border-left-color: #057a55;
        }

        .sinistre-card.douteux {
            border-left-color: #ff8800;
        }

        .sinistre-card.frauduleux {
            border-left-color: #e02424;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 8px;
        }

        .card-head h3 {
            font-size: 1rem;
            font-weight: 700;
        }

        .card-meta {
            display: flex;
            gap: 16px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .card-meta span {
            font-size: .83rem;
            color: #6b7280;
        }

        .card-meta strong {
            color: #111827;
        }

        .score-bar-wrap {
            margin-top: 12px;
        }

        .score-bar-bg {
            background: #f3f4f6;
            border-radius: 999px;
            height: 8px;
            overflow: hidden;
        }

        .score-bar-fill {
            height: 100%;
            border-radius: 999px;
            transition: width .5s;
        }

        .score-bar-fill.normal {
            background: #057a55;
        }

        .score-bar-fill.douteux {
            background: #ff8800;
        }

        .score-bar-fill.frauduleux {
            background: #e02424;
        }

        .score-label {
            font-size: .78rem;
            color: #6b7280;
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
        }

        .nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e2a3a;
            padding: 14px 28px;
            color: #fff;
        }

        .nav-bar a {
            color: #9ca3af;
            text-decoration: none;
            font-size: .9rem;
            margin-left: 20px;
        }

        .nav-bar a:hover {
            color: #fff;
        }

        .nav-bar a.active {
            color: #60a5fa;
        }
    </style>
</head>

<body>

    <div class="nav-bar">
        <span>🛡️ FraudShield</span>
        <div>
            <a href="declarer.php">➕ Déclarer</a>
            <a href="historique.php" class="active">📋 Mes sinistres</a>
            <a href="logout.php">🚪 Déconnexion</a>
        </div>
    </div>

    <div class="wrap">

        <!-- Infos utilisateur -->
        <div class="user-info">
            <div class="avatar">
                <?= strtoupper(substr($user['prenom'], 0, 1)) ?>
            </div>
            <div class="user-meta" style="flex:1">
                <h3>
                    <?= clean($user['prenom'] . ' ' . $user['nom']) ?>
                </h3>
                <p>
                    <?= clean($user['email']) ?> ·
                    <?= clean($user['telephone'] ?? '—') ?>
                </p>
            </div>
            <div>
                <span class="score-pill <?= $user['statut'] ?>">
                    Score global :
                    <?= $user['score_global'] ?> pts
                </span>
            </div>
        </div>

        <!-- Barre du haut -->
        <div class="top-bar">
            <h2 class="page-title" style="margin:0">
                📋 Mes déclarations
                <span style="font-size:.9rem;color:#6b7280;font-weight:400">(
                    <?= count($sinistres) ?>)
                </span>
            </h2>
            <a href="declarer.php" class="btn-sm btn-blue">➕ Nouvelle déclaration</a>
        </div>

        <!-- Liste sinistres -->
        <?php if (empty($sinistres)): ?>
            <div class="empty-state">
                <span>📭</span>
                <p>Aucune déclaration pour le moment.</p>
                <a href="declarer.php" class="btn-sm btn-blue" style="margin-top:16px">Faire ma première déclaration</a>
            </div>
        <?php else: ?>
            <?php foreach ($sinistres as $s):
                $pct = min(100, round($s['score_sinistre']));
                ?>
                <div class="sinistre-card <?= $s['statut'] ?>">
                    <div class="card-head">
                        <h3>📌
                            <?= clean($s['type_sinistre']) ?>
                        </h3>
                        <?= badgeStatut($s['statut']) ?>
                    </div>

                    <div class="card-meta">
                        <span>🗓️ Sinistre le <strong>
                                <?= date('d/m/Y', strtotime($s['date_sinistre'])) ?>
                            </strong></span>
                        <span>📤 Déclaré le <strong>
                                <?= date('d/m/Y à H:i', strtotime($s['date_declaration'])) ?>
                            </strong></span>
                        <span>💰 <strong>
                                <?= number_format($s['montant_estime'], 0, ',', ' ') ?> FCFA
                            </strong></span>
                        <?php if ($s['localisation']): ?>
                            <span>📍
                                <?= clean($s['localisation']) ?>
                            </span>
                        <?php endif; ?>
                        <span>📎
                            <?= $s['nb_preuves'] ?> pièce(s) jointe(s)
                        </span>
                    </div>

                    <?php if ($s['description']): ?>
                        <p style="margin-top:10px;font-size:.88rem;color:#374151;line-height:1.5">
                            <?= nl2br(clean(substr($s['description'], 0, 200))) ?>
                            <?= strlen($s['description']) > 200 ? '…' : '' ?>
                        </p>
                    <?php endif; ?>

                    <!-- Barre de score -->
                    <div class="score-bar-wrap">
                        <div class="score-bar-bg">
                            <div class="score-bar-fill <?= $s['statut'] ?>" style="width:<?= $pct ?>%"></div>
                        </div>
                        <div class="score-label">
                            <span>Score de risque : <strong>
                                    <?= $s['score_sinistre'] ?> pts
                                </strong></span>
                            <span>
                                <?php if ($s['statut'] === 'normal'): ?>✅ Dossier normal
                                <?php elseif ($s['statut'] === 'douteux'): ?>⚠️ Vérification en cours
                                <?php else: ?>🚨 Enquête ouverte
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($s['commentaire_admin']): ?>
                        <div class="alert warning" style="margin-top:12px;margin-bottom:0">
                            💬 Note de l'agent :
                            <?= clean($s['commentaire_admin']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>

</html>