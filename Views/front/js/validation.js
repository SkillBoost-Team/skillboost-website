document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        const email = form.email.value.trim();
        const motDePasse = form.mot_de_passe.value.trim();

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            alert("Veuillez saisir une adresse e-mail valide.");
            e.preventDefault();
            return;
        }

        if (motDePasse.length < 4) {
            alert("Le mot de passe doit contenir au moins 4 caractères.");
            e.preventDefault();
            return;
        }
    });
});
