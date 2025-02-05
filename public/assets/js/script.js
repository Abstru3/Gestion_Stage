// Fonction pour valider le formulaire d'inscription
function validateRegistrationForm() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var email = document.getElementById('email').value;

    // Validation du nom d'utilisateur
    if (username.length < 3) {
        alert("Le nom d'utilisateur doit contenir au moins 3 caractères.");
        return false;
    }

    // Validation du mot de passe
    if (password.length < 6) {
        alert("Le mot de passe doit contenir au moins 6 caractères.");
        return false;
    }

    // Vérification que le mot de passe contient à la fois des lettres et des chiffres
    if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(password)) {
        alert("Le mot de passe doit contenir au moins une lettre et un chiffre.");
        return false;
    }

    // Validation de l'email avec une expression régulière
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(email)) {
        alert("Veuillez entrer une adresse email valide.");
        return false;
    }

    return true;
}
