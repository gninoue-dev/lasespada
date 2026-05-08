/* =========================
   FILTRES ALERTES
========================= */

const buttons = document.querySelectorAll(".filter-btn");
const alerts = document.querySelectorAll(".alert");

buttons.forEach((btn) => {
  btn.addEventListener("click", () => {
    buttons.forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");

    const filter = btn.dataset.filter;

    alerts.forEach((alert) => {
      if (filter === "all") {
        alert.style.display = "flex";
      } else {
        alert.style.display = alert.dataset.type === filter ? "flex" : "none";
      }
    });
  });
});

/* =========================
   VOIR SINISTRE
========================= */

function goSinistre(id) {
  // lien vers ton module sinistre existant
  window.location.href = "detail_sinistres.php?id=" + id;
}

/* =========================
   MARQUER COMME TRAITÉ
========================= */

function markDone(btn) {
  let alert = btn.closest(".alert");
  alert.style.opacity = "0.5";
  alert.style.borderLeftColor = "#16a34a";
  alert.querySelector(".alert-text").innerHTML += " ✔ traité";
}

/* =========================
   SUPPRIMER / IGNORER
========================= */

function removeAlert(btn) {
  let alert = btn.closest(".alert");
  alert.remove();
}

/* =========================
   SIMULATION TEMPS RÉEL
========================= */

// Exemple : nouvelle alerte automatique
setTimeout(() => {
  let list = document.getElementById("alertList");

  let newAlert = document.createElement("div");
  newAlert.className = "alert warning";
  newAlert.dataset.type = "warning";

  newAlert.innerHTML = `
        <div class="alert-text">
            ⚠ Nouvelle activité suspecte - client3
        </div>
        <div>
            <button class="btn view" onclick="goSinistre(4)">Voir</button>
            <button class="btn done" onclick="markDone(this)">Traiter</button>
            <button class="btn ignore" onclick="removeAlert(this)">Ignorer</button>
        </div>
    `;

  list.prepend(newAlert);
}, 4000);
