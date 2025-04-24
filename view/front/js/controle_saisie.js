<script>
    // Fonction pour valider le formulaire en JavaScript
    function validateForm(event) {
        var titre = document.forms["postForm"]["titre"].value;
        var description = document.forms["postForm"]["description"].value;
        var dateEvenement = document.forms["postForm"]["date_evenement"].value;
        var typeEvenement = document.forms["postForm"]["type_evenement"].value;
        var lieuOuLien = document.forms["postForm"]["lieu_ou_lien"].value;
        var statut = document.forms["postForm"]["statut"].value;
        var errorMessage = '';
        var isValid = true;

        // Réinitialiser les styles d'erreur
        document.getElementById("titre").style.borderColor = "";
        document.getElementById("description").style.borderColor = "";
        document.getElementById("date_evenement").style.borderColor = "";
        document.getElementById("type_evenement").style.borderColor = "";
        document.getElementById("lieu_ou_lien").style.borderColor = "";
        document.getElementById("statut").style.borderColor = "";

        // Vérification du titre
        var titreRegex = /^[a-zA-ZÀ-ÿ0-9\s\-_,.!?']+$/;
        if (!titre || titre.trim().length <= 2) {
            errorMessage += "Le titre doit contenir plus de 2 caractères.\n";
            document.getElementById("titre").style.borderColor = "red";
            isValid = false;
        } else if (!titreRegex.test(titre)) {
            errorMessage += "Le titre contient des caractères non autorisés.\n";
            document.getElementById("titre").style.borderColor = "red";
            isValid = false;
        }

        // Vérification de la description
        if (!description || description.trim().length <= 10) {
            errorMessage += "La description doit contenir plus de 10 caractères.\n";
            document.getElementById("description").style.borderColor = "red";
            isValid = false;
        }

        // Vérification de la date de l'événement
        if (!dateEvenement) {
            errorMessage += "Veuillez saisir une date pour l'événement.\n";
            document.getElementById("date_evenement").style.borderColor = "red";
            isValid = false;
        } else {
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var selectedDate = new Date(dateEvenement);
            
            if (selectedDate < today) {
                errorMessage += "La date de l'événement doit être dans le futur.\n";
                document.getElementById("date_evenement").style.borderColor = "red";
                isValid = false;
            }
        }

        // Vérification du type d'événement
        if (!typeEvenement) {
            errorMessage += "Veuillez sélectionner un type d'événement.\n";
            document.getElementById("type_evenement").style.borderColor = "red";
            isValid = false;
        }

        // Vérification du lieu ou lien
        if (!lieuOuLien || lieuOuLien.trim().length <= 2) {
            errorMessage += "Le lieu ou lien doit contenir plus de 2 caractères.\n";
            document.getElementById("lieu_ou_lien").style.borderColor = "red";
            isValid = false;
        }

        // Vérification du statut
        if (!statut) {
            errorMessage += "Veuillez sélectionner un statut.\n";
            document.getElementById("statut").style.borderColor = "red";
            isValid = false;
        }

        // Afficher le message d'erreur ou de succès
        if (!isValid) {
            alert("Erreurs dans le formulaire:\n" + errorMessage);
            event.preventDefault();
        } else {
            alert("L'événement a été ajouté avec succès!");
            // Le formulaire sera soumis normalement si tout est valide
        }
    }
</script>