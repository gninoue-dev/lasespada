// ✅ Serotonyn — JS (CodePen)
// Demo data + rendu KPI + sparklines + export CSV
// Branche tes vraies datas en remplaçant `getMockData()`.

const fmtPct = (n) => `${Math.round(n)}%`;
const fmtInt = (n) => `${Math.round(n).toLocaleString("fr-FR")}`;
const fmtWeeks = (n) => `${n.toFixed(1).replace(".", ",")} sem.`;
const fmtTrend = (n) => (n >= 0 ? `+${n.toFixed(1).replace(".", ",")}` : `${n.toFixed(1).replace(".", ",")}`);

const nowFR = () => {
  const d = new Date();
  return d.toLocaleString("fr-FR", { dateStyle: "medium", timeStyle: "short" });
};

document.getElementById("lastUpdate").textContent = nowFR();

const kpis = [
  {
    id: "activation",
    name: "Taux d’activation réelle",
    desc: "Part des inscrits ayant activé au moins un objectif (preuve d’intention transformée en action).",
    badge: "Adoption",
    valueFmt: fmtPct,
    explain: "Indique si le dispositif est réellement lancé, au-delà du simple déploiement.",
    dont: "Ne pas interpréter comme “motivation individuelle”.",
  },
  {
    id: "active_30_90",
    name: "Utilisateurs actifs (J30 / J90)",
    desc: "Part des utilisateurs encore actifs dans la durée (anti “effet gadget”).",
    badge: "Tenue",
    valueFmt: (v) => `${fmtPct(v.j30)} / ${fmtPct(v.j90)}`,
    explain: "Mesure la continuité d’usage : c’est là que la prévention se joue.",
    dont: "Ne pas comparer à une autre entreprise (contextes différents).",
  },
  {
    id: "routines_stable_plus",
    name: "Routines stabilisées ou plus",
    desc: "Part des routines qui ont dépassé le simple “test” (stabilisation, automatisation, maintien).",
    badge: "Habitudes",
    valueFmt: fmtPct,
    explain: "Preuve comportementale : les habitudes s’installent, pas juste des intentions.",
    dont: "Ne pas conclure à un “résultat santé”.",
  },
  {
    id: "time_to_stabilize",
    name: "Temps moyen pour stabiliser",
    desc: "Délai moyen pour qu’une routine devienne fiable dans la vraie vie.",
    badge: "Réalisme",
    valueFmt: fmtWeeks,
    explain: "Aide à calibrer les programmes : un changement durable prend du temps.",
    dont: "Ne pas viser “plus vite” à tout prix (risque de décrochage).",
  },
  {
    id: "wellbeing_trend",
    name: "Tendance de bien-être perçu (agrégé)",
    desc: "Évolution globale perçue (énergie / stress / récupération) — sans médicalisation.",
    badge: "Ressenti",
    valueFmt: (v) => `${v.label}`,
    explain: "Indique si la dynamique est plutôt favorable, stable ou sous tension.",
    dont: "Ne pas interpréter comme diagnostic ou mesure clinique.",
  },
  {
    id: "recovery_rate",
    name: "Taux de reprise après décrochage",
    desc: "Part des utilisateurs qui reviennent après une pause (la vraie vie inclut des ruptures).",
    badge: "Résilience",
    valueFmt: fmtPct,
    explain: "Différenciateur fort : robustesse de l’accompagnement au contexte réel.",
    dont: "Ne pas blâmer l’équipe ou les salariés en cas de baisse (contexte).",
  },
  {
    id: "small_steps",
    name: "Petits pas réalisés (jours d’action)",
    desc: "Nombre total de jours où au moins une micro-action a été réalisée (agrégé sur la période).",
    badge: "Action",
    valueFmt: fmtInt,
    explain: "Indicateur positif : accumulation d’actions concrètes, même minimales.",
    dont: "Ne pas transformer en objectif de performance ou en classement.",
  }
];

function getMockSeries(len=12, base=50, volatility=10){
  const arr = [];
  let v = base;
  for(let i=0;i<len;i++){
    v = Math.max(0, Math.min(100, v + (Math.random()*2-1)*volatility));
    arr.push(v);
  }
  return arr;
}

function getMockData(periodDays){
  // Simule des données cohérentes entre elles
  const activation = 52 + Math.random()*25; // %
  const j30 = 28 + Math.random()*30;
  const j90 = Math.max(8, j30 - (8 + Math.random()*15));

  const routinesStable = 30 + Math.random()*35;

  const tStab = 3.2 + Math.random()*2.8; // weeks

  const wbScore = -0.6 + Math.random()*1.6; // trend
  const wbLabel = wbScore > 0.4 ? "En amélioration" : (wbScore < -0.2 ? "Sous tension" : "Plutôt stable");

  const recovery = 35 + Math.random()*35;

  const steps = Math.round((periodDays/30) * (700 + Math.random()*1100)); // “jours d’action” agrégés

  return {
    org: "Entreprise X",
    periodDays,
    values: {
      activation: activation,
      active_30_90: { j30, j90 },
      routines_stable_plus: routinesStable,
      time_to_stabilize: tStab,
      wellbeing_trend: { score: wbScore, label: wbLabel },
      recovery_rate: recovery,
      small_steps: steps
    },
    series: {
      activation: getMockSeries(12, activation, 6),
      active_30_90: getMockSeries(12, j30, 7),
      routines_stable_plus: getMockSeries(12, routinesStable, 8),
      time_to_stabilize: getMockSeries(12, 100 - (tStab*10), 5), // inversé visuellement
      wellbeing_trend: getMockSeries(12, 55 + wbScore*10, 5),
      recovery_rate: getMockSeries(12, recovery, 9),
      small_steps: getMockSeries(12, 60, 12)
    }
  };
}

function sparklineSVG(points){
  const w = 120, h = 44, pad = 4;
  const min = Math.min(...points), max = Math.max(...points);
  const norm = (v) => {
    if (max === min) return h/2;
    const t = (v - min) / (max - min);
    return pad + (1 - t) * (h - 2*pad);
  };

  const xs = points.map((_, i) => pad + i * ((w - 2*pad) / (points.length - 1)));
  const ys = points.map(norm);

  let d = `M ${xs[0].toFixed(2)} ${ys[0].toFixed(2)}`;
  for(let i=1;i<points.length;i++){
    d += ` L ${xs[i].toFixed(2)} ${ys[i].toFixed(2)}`;
  }

  // Area (soft)
  const area = `${d} L ${w-pad} ${h-pad} L ${pad} ${h-pad} Z`;

  return `
    <svg class="spark" viewBox="0 0 ${w} ${h}" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="${area}" fill="rgba(110,231,255,0.12)"></path>
      <path d="${d}" stroke="rgba(255,255,255,0.85)" stroke-width="2.2" stroke-linecap="round"></path>
      <circle cx="${xs[xs.length-1].toFixed(2)}" cy="${ys[ys.length-1].toFixed(2)}" r="3.2" fill="rgba(167,139,250,0.95)"></circle>
    </svg>
  `;
}

function deltaChip(delta){
  const cls = delta > 0.8 ? "good" : (delta < -0.4 ? "bad" : "warn");
  const label = delta > 0.8 ? "En hausse" : (delta < -0.4 ? "En baisse" : "Stable");
  return `<div class="chip ${cls}">${label} • ${fmtTrend(delta)}</div>`;
}

function render(data){
  document.getElementById("orgName").textContent = data.org;

  const grid = document.getElementById("kpiGrid");
  grid.innerHTML = "";

  kpis.forEach(k => {
    const v = data.values[k.id];
    const s = data.series[k.id] || getMockSeries();

    // delta = last - mean (quick trend)
    const last = s[s.length-1];
    const mean = s.reduce((a,b)=>a+b,0) / s.length;
    const delta = (last - mean) / 10; // scaled to keep readable

    const value = k.valueFmt(v);
    const card = document.createElement("div");
    card.className = "card";
    card.innerHTML = `
      <div class="kpi-head">
        <div class="kpi-title">
          <div class="name">${k.name}</div>
          <div class="desc">${k.desc}</div>
        </div>
        <div class="badge">${k.badge}</div>
      </div>

      <div class="kpi-body">
        <div>
          <div class="value">${value}</div>
          <div class="sub">${k.explain}</div>
        </div>
        <div class="delta">
          ${deltaChip(delta)}
          ${sparklineSVG(s)}
        </div>
      </div>
    `;
    grid.appendChild(card);
  });

  // Insights (3 phrases pour “comprendre tout de suite”)
  const insights = [];
  const act = data.values.activation;
  const cont = data.values.active_30_90;
  const stable = data.values.routines_stable_plus;
  const wb = data.values.wellbeing_trend;
  const rec = data.values.recovery_rate;
  const steps = data.values.small_steps;

  insights.push(
    act >= 60
      ? `✅ <strong>Adoption solide :</strong> ${fmtPct(act)} des inscrits ont activé un objectif (le dispositif est réellement lancé).`
      : `🟡 <strong>Adoption à renforcer :</strong> ${fmtPct(act)} d’activation. Le levier prioritaire est l’onboarding & la communication interne.`
  );

  insights.push(
    stable >= 55
      ? `✅ <strong>Habitudes en train de s’installer :</strong> ${fmtPct(stable)} des routines dépassent le simple test (stabilisation ou plus).`
      : `🟡 <strong>Phase “test” dominante :</strong> ${fmtPct(stable)} seulement en routines stabilisées+. Ajuster la charge et renforcer l’accompagnement.`
  );

  insights.push(
    wb.label === "En amélioration"
      ? `✅ <strong>Dynamique perçue favorable :</strong> tendance “${wb.label}”. ${fmtInt(steps)} jours d’action sur la période, avec ${fmtPct(rec)} de reprises après pause (robustesse).`
      : (wb.label === "Sous tension"
          ? `🟠 <strong>Contexte sous tension :</strong> tendance “${wb.label}”. Recommandé : alléger les routines + renforcer la reconnection.`
          : `🟡 <strong>Dynamique stable :</strong> tendance “${wb.label}”. ${fmtInt(steps)} jours d’action, et ${fmtPct(rec)} de reprises après pause (bon signal de résilience).`
        )
  );

  const ul = document.getElementById("insights");
  ul.innerHTML = insights.map(x => `<li>${x}</li>`).join("");

  // Definitions
  const defs = document.getElementById("defs");
  defs.innerHTML = kpis.map(k => `
    <div class="def">
      <div class="t">${k.name}</div>
      <div class="p">${k.explain}</div>
      <div class="dont"><span>À ne pas faire :</span> ${k.dont}</div>
    </div>
  `).join("");
}

function exportCSV(data){
  const rows = [
    ["organisation", data.org],
    ["periode_jours", data.periodDays],
    ["activation_pct", data.values.activation],
    ["actifs_j30_pct", data.values.active_30_90.j30],
    ["actifs_j90_pct", data.values.active_30_90.j90],
    ["routines_stable_plus_pct", data.values.routines_stable_plus],
    ["temps_moyen_stabilisation_sem", data.values.time_to_stabilize],
    ["tendance_bien_etre_label", data.values.wellbeing_trend.label],
    ["tendance_bien_etre_score", data.values.wellbeing_trend.score],
    ["taux_reprise_pct", data.values.recovery_rate],
    ["petits_pas_jours_action_total", data.values.small_steps],
  ];

  const csv = rows.map(r => r.map(x => `"${String(x).replaceAll('"','""')}"`).join(";")).join("\n");
  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `serotonyn_dashboard_${data.org.replaceAll(" ","_")}_${data.periodDays}j.csv`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

// init
let state = getMockData(30);
render(state);

document.getElementById("period").addEventListener("change", (e) => {
  const days = Number(e.target.value);
  state = getMockData(days);
  render(state);
});

document.getElementById("population").addEventListener("change", () => {
  // Démo: on re-randomise. En prod: filtre dataset
  state = getMockData(state.periodDays);
  render(state);
});

document.getElementById("exportBtn").addEventListener("click", () => exportCSV(state));

