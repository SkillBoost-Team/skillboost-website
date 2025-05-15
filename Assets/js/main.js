// Initialisation générale
$(document).ready(function() {
    // Gestion du spinner
    $(window).on('load', function() {
        $('#spinner').fadeOut();
    });

    // Activation des tooltips Bootstrap
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Gestion FAQ
    $('.faq-question').click(function() {
        $(this).toggleClass('active').next('.faq-answer').slideToggle();
    });
});

// Gestion des messages flash
setTimeout(() => {
    $('.alert').fadeOut();
}, 5000);