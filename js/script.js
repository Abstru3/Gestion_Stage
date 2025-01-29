// Fonction pour valider le formulaire d'inscription
function validateRegistrationForm() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var email = document.getElementById('email').value;

    if (username.length < 3) {
        alert("Le nom d'utilisateur doit contenir au moins 3 caractères.");
        return false;
    }

    if (password.length < 6) {
        alert("Le mot de passe doit contenir au moins 6 caractères.");
        return false;
    }

    if (!email.includes('@')) {
        alert("Veuillez entrer une adresse email valide.");
        return false;
    }

    return true;
}

// Ajouter d'autres fonctions JavaScript selon les besoins