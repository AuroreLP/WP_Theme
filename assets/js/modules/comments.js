/**
 * Gestion des formulaires de commentaires
 */
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons "Répondre"
    const replyLinks = document.querySelectorAll('.reply-link');
    
    replyLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const commentId = parseInt(this.getAttribute('data-comment-id'), 10);
            
            if (!commentId) return;
            
            // Cacher tous les formulaires de réponse
            const allForms = document.querySelectorAll('.comment-reply-form');
            allForms.forEach(function(form) {
                form.style.display = 'none';
            });
            
            // Afficher le formulaire de réponse cliqué
            const form = document.getElementById('comment-reply-form-' + commentId);
            if (form) {
                form.style.display = 'block';
            }
        });
    });
});