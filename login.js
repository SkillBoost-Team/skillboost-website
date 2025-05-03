document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");

    form.addEventListener("submit", function (event) {
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;

        const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;

        // Vérification de l'email
        if (!emailRegex.test(email)) {
            alert("❌ Veuillez entrer une adresse email valide.");
            event.preventDefault();
            return;
        }

        // Vérification du mot de passe
        if (password.length < 6) {
            alert("❌ Le mot de passe doit contenir au moins 6 caractères.");
            event.preventDefault();
            return;
        }

        // Toutes les vérifications sont passées, le formulaire peut être soumis
    });
});
