/**
 * Share Buttons — Copy Link handler
 *
 * Copies the current article URL to the clipboard when the user clicks
 * the copy button (.share-btn--copy). Provides brief visual feedback
 * by swapping the icon to a checkmark for 2 seconds.
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var copyButtons = document.querySelectorAll( '.share-btn--copy' );

    copyButtons.forEach( function ( btn ) {
        btn.addEventListener( 'click', function () {
            var url = btn.dataset.url;
            if ( ! url || ! navigator.clipboard ) {
                return;
            }

            navigator.clipboard.writeText( url ).then( function () {
                var icon = btn.querySelector( 'ion-icon' );
                if ( icon ) {
                    icon.setAttribute( 'name', 'checkmark-outline' );
                    setTimeout( function () {
                        icon.setAttribute( 'name', 'link-outline' );
                    }, 2000 );
                }
            } );
        } );
    } );
} );
