

/* Exemple futur :
- envoyer validation vers PHP
- update base de données
*/

document.querySelector(".btn-validate").addEventListener("click", () => {
    alert("Sinistre validé (à connecter à la BDD)");
});

document.querySelector(".btn-refuse").addEventListener("click", () => {
    alert("Sinistre refusé (à connecter à la BDD)");
});

document.querySelector(".btn-warn").addEventListener("click", () => {
    alert("Sinistre mis en attente");
});

