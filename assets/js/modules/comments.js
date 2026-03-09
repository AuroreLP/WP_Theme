/**
 * Comment Reply Form Toggle
 *
 * Handles the "Répondre" (Reply) button behavior on comment cards.
 * When clicked, shows the inline reply form for that specific comment
 * and hides any other open reply form (only one visible at a time).
 *
 * Works with the comment HTML structure in comments.php:
 * - Each comment has a .reply-link with a data-comment-id attribute
 * - Each comment has a .comment-reply-form with a matching ID
 *
 * Loaded conditionally on single posts and chroniques (see enqueue.php).
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var replyLinks = document.querySelectorAll( '.reply-link' );

    replyLinks.forEach( function ( link ) {
        link.addEventListener( 'click', function ( e ) {
            e.preventDefault();

            var commentId = parseInt( this.getAttribute( 'data-comment-id' ), 10 );
            if ( ! commentId ) {
                return;
            }

            // Close all open reply forms (one visible at a time)
            var allForms = document.querySelectorAll( '.comment-reply-form' );
            allForms.forEach( function ( form ) {
                form.style.display = 'none';
            });

            // Open the reply form for the clicked comment
            var form = document.getElementById( 'comment-reply-form-' + commentId );
            if ( form ) {
                form.style.display = 'block';
            }
        });
    });
});
