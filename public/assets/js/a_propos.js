console.log('a_propos.js chargé');

document.addEventListener("DOMContentLoaded", function() {
    const title = document.querySelector('header h1');
    title.classList.add('fade-in'); 

    const teamMembers = document.querySelectorAll('.member');
    teamMembers.forEach((member, index) => {
        setTimeout(() => {
            member.classList.add('fade-in-delay'); 
        }, index * 500); 
    });
});
