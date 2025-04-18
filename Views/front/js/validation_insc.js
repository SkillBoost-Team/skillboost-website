// js/validation.js

document.addEventListener('DOMContentLoaded', () => {
    const formInscription = document.querySelector('form');
    if (!formInscription) return;

    formInscription.addEventListener('submit', (e) => {
        const nom = formInscription.querySelector('input[name="nom"]').value.trim();
        const email = formInscription.querySelector('input[name="email"]').value.trim();
        const motDePasse = formInscription.querySelector('input[name="mot_de_passe"]').value.trim();
        const role = formInscription.querySelector('select[name="role"]').value;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        let erreurs = [];

        if (nom.length < 3) {
            erreurs.push("Le nom doit contenir au moins 3 caractères.");
        }

        if (!emailRegex.test(email)) {
            erreurs.push("Email invalide.");
        }

        if (motDePasse.length < 6) {
            erreurs.push("Le mot de passe doit contenir au moins 6 caractères.");
        }

        if (!role) {
            erreurs.push("Veuillez sélectionner un rôle.");
        }

        if (erreurs.length > 0) {
            e.preventDefault();
            alert("⚠️ Erreurs trouvées :\n\n" + erreurs.join("\n"));
        }
    });
});
