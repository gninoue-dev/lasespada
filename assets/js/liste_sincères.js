

/* ===== RECHERCHE ===== */
document.getElementById("search").addEventListener("input", function(){

    let value = this.value.toLowerCase();

    document.querySelectorAll("#table tr").forEach(row => {

        let client = row.children[1].textContent.toLowerCase();

        row.style.display = client.includes(value) ? "" : "none";

    });

});

/* ===== FILTRES ===== */
document.querySelectorAll(".filter").forEach(btn => {

    btn.addEventListener("click", () => {

        document.querySelectorAll(".filter").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        let filter = btn.dataset.filter;

        document.querySelectorAll("#table tr").forEach(row => {

            if(filter === "all"){
                row.style.display = "";
            } else {
                row.style.display = row.dataset.status === filter ? "" : "none";
            }

        });

    });

});

/* ===== REDIRECTION DETAIL ===== */
function goDetail(id){
    window.location.href = "detail_sinistres.php?id=" + id;
}

