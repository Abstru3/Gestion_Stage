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

    if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(password)) {
        alert("Le mot de passe doit contenir au moins une lettre et un chiffre.");
        return false;
    }

    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(email)) {
        alert("Veuillez entrer une adresse email valide.");
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');

    searchForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const searchQuery = searchInput.value.trim();
        if (searchQuery) {
            const url = `/Gestion_Stage/app/views/internships/all_internships.php?search=${encodeURIComponent(searchQuery)}&sort=date_debut&order=ASC`;
            window.location.href = url;
        }
    });
});