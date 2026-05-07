<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="../assets/css/dashbord.css">
        <script src="../assets/js/dashbord.js"></script>
</head>
<body>

<!-- ✅ Serotonyn — Dashboard Client (B2B) | CodePen-ready (HTML only)
     ➜ Colle ce bloc dans l’onglet HTML de CodePen.
     ➜ Mets le CSS dans l’onglet CSS et le JS dans l’onglet JS.
     ⚠️ Charte Serotonyn: je n’ai pas ta PJ ici, donc j’ai créé un thème “clean santé”
     avec variables CSS à remplacer en 30 secondes (colors + font).
-->

<div class="app">
  <header class="topbar">
    <div class="brand">
      <div class="logo">
        <span class="logo-dot">
            <img src="../assets/images/user-sign-icon-front-side-removebg-preview.png" alt="">
        </span>
        <span class="logo-word">nom du client</span>
        
      </div>
      <div class="subtitle">Compte Securisée</div>
    </div>

    <div class="controls">
      <div class="control">
        <label for="period">status du compte</label>
        <p class="status">normal</p>
     
      </div>
      <div class="control">
        <label for="population">historique des sinistres</label>
        <p class="population">3 sinistres sur les 12 derniers mois</p>
      </div>
      <button id="exportBtn" class="btn">voir sinistres</button>
    </div>
  </header>

  <main class="content">
    <section class="hero">
      <div class="hero-left">
        <h1>Vue d’ensemble</h1>
        <p class="hero-note">
          declares des sinistres en toute securité <strong>application de sevère en cas se fraude</strong>, la <strong>dynamique d’habitudes</strong> et
          l’<strong>impact perçu</strong> — <strong>prises en charges</strong>, cotisation a jour.
        </p>
      </div>
      <div class="hero-right">
        <div class="pill">
          <span class="pill-dot"></span>
          Adresse • Abidjan • Rivera
        </div>
        <div class="meta">
          <div><span class="muted">Numéro d'assurée</span> <strong id="orgName">N°id4343535</strong></div>
          <div><span class="muted">Dernière mise à jour :</span> <strong id="lastUpdate">il y a 10 minutes</strong></div>
        </div>
      </div>
    </section>

    <section class="grid" id="kpiGrid"></section>

    <section class="details">
      <div class="panel">
        <div class="panel-head">
          <h2>Score de risque :</h2>
          <div class="muted">O</div>
        </div>
        <ul class="insights" id="insights"></ul>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h2>Staut du Compte</h2>
          <div class="muted">normal</div>
        </div>
        <div class="defs" id="defs"></div>
      </div>
    </section>

    <footer class="footer">
      <div class="muted">
       <button class="btn" style="font-size: 1rem;">Déclarer un sinistre +</button>
      </div>
    </footer>
  </main>
</div>
</body>
</html>