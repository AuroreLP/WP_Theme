/**
 * Excerpt Character & Word Counter
 *
 * Adds a live counter below the excerpt textarea in the WordPress
 * post editor, showing both word count and character count.
 * Updates on every keystroke.
 *
 * jQuery is used here because the WordPress admin always loads jQuery,
 * and this script only runs on admin post editing screens
 * (post.php and post-new.php — see enqueue in post-types.php).
 *
 * @package turningpages
 */

jQuery( document ).ready( function ( $ ) {

    var $excerptField = $( '#excerpt' );
    if ( ! $excerptField.length ) {
        return;
    }

    // Insert counter element below the textarea
    var $counter = $( '<div id="excerpt-counter" style="margin-top:5px; font-size:0.9em; color:#555;"></div>' );
    $excerptField.after( $counter );

    function updateCounter() {
        var text      = $excerptField.val();
        var wordCount = text.trim().split( /\s+/ ).filter( Boolean ).length;
        var charCount = text.length;

        $counter.text( 'Mots: ' + wordCount + ' | Caractères: ' + charCount );
    }

    // Initial count + live updates
    updateCounter();
    $excerptField.on( 'input', updateCounter );
});
