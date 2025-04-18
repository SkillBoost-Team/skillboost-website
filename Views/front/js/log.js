document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    if (email === "test@example.com" && password === "123456") {
        alert("Connexion réussie !");
        window.location.href = "index.html";  // Redirection après connexion
    } else {
        alert("Email ou mot de passe incorrect !");
    }
});
function loadPage(page) {
    fetch(page)
        .then(response => response.text())
        .then(html => {
            document.getElementById("main-content").innerHTML = html;

            // Charger dynamiquement le script JS de la page si nécessaire
            if (page === "reclamations.html") {
                let script = document.createElement("script");
                script.src = "js/reclamations.js";
                document.body.appendChild(script);
            }
        });
}
