$(document).ready(function() {
    function scrollToBottom() {
        var messagesContainer = document.getElementById('messageContainer');
        if(messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    // Expose the function to global scope for AJAX callback
    window.scrollToBottom = scrollToBottom;
});

function loadMessages(expediteur_id) {
    $.ajax({
        url: "load_messages.php",
        method: "POST",
        data: { expediteur_id },
        success: function(response) {
            $("#messageContent").html(response);
            scrollToBottom(); // Appeler la fonction pour faire défiler le contenu vers le bas
        }
    });
}

// Si tu as une fonction qui envoie des messages (par exemple via un formulaire ou bouton)
$(document).on('submit', '#messageForm', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: "send_message.php",  // Assurez-vous que cette URL est correcte pour envoyer les messages
        method: "POST",
        data: $(this).serialize(),  // Sérialisation du formulaire pour envoyer les données
        success: function(response) {
            // Ajouter ici le code pour afficher le nouveau message dans la conversation
            // Exemple : $(response) pourrait être le message ajouté, selon ton code
            $(".messages").append(response);
            scrollToBottom();  // Défiler vers le bas après l'envoi du message
        }
    });
});

