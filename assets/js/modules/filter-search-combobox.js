/**
 * Search-to-filter combobox overlay — Chroniques listing pages
 *
 * With filter dropdowns fed by real content (tp_get_terms_for_posts()),
 * #filter-genre / #filter-theme / #filter-nation can hold dozens of flat,
 * alphabetical options — hard to scan without a search box. #filter-media
 * is intentionally left alone: it never has enough options to need one.
 *
 * Design: this script does NOT replace the <select> elements. Each one
 * stays in the DOM, untouched, as the single source of truth that
 * filter-chroniques.js reads via .value and listens to via 'change'.
 * On load, this script builds a visual "search + list" combobox next to
 * each targeted select, then hides the select (display: none via the
 * `hidden` attribute) so only the combobox is interactive. Selecting an
 * option in the combobox sets select.value and dispatches a native
 * 'change' event — filter-chroniques.js reacts exactly as if the user
 * had picked an option in the native dropdown. This file never touches
 * filter-chroniques.js and knows nothing about posts/cards/pagination.
 *
 * If JS fails to run, the native <select> is never hidden and remains
 * fully usable (progressive enhancement).
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var TARGET_IDS = [ 'filter-genre', 'filter-theme', 'filter-nation' ];

    TARGET_IDS.forEach( function ( id ) {
        var select = document.getElementById( id );
        if ( select ) {
            buildCombobox( select );
        }
    });

    /**
     * Strip accents and lowercase, so "eco" matches "Écologie".
     */
    function normalize( str ) {
        return str
            .normalize( 'NFD' )
            .replace( /[\u0300-\u036f]/g, '' )
            .toLowerCase();
    }

    /**
     * Turn the select's own placeholder label ("Par thème") into an
     * unambiguous reset label ("Tous les thèmes"), generated once from
     * that text rather than hardcoded per filter. "Pays" already ends
     * in "s" so it's left untouched.
     */
    function buildResetLabel( placeholderText ) {
        var noun   = placeholderText.replace( /^Par\s+/i, '' );
        var plural = /s$/i.test( noun ) ? noun : noun + 's';
        return 'Tous les ' + plural;
    }

    /**
     * Build one search-to-filter combobox in front of the given <select>,
     * then hide the select from view (it stays in the DOM and keeps
     * receiving .value updates + 'change' events).
     */
    function buildCombobox( select ) {
        var options = Array.from( select.options );
        if ( ! options.length ) {
            return;
        }

        var placeholder = options[0]; // first option ("Par genre" / "Par thème" / "Par pays")

        var wrapper = document.createElement( 'div' );
        wrapper.className = 'filter-combobox';

        var trigger = document.createElement( 'button' );
        trigger.type = 'button';
        trigger.className = 'filter-combobox-trigger';
        trigger.setAttribute( 'aria-haspopup', 'listbox' );
        trigger.setAttribute( 'aria-expanded', 'false' );
        trigger.textContent = placeholder.textContent;

        var panel = document.createElement( 'div' );
        panel.className = 'filter-combobox-panel';
        panel.hidden = true;

        var search = document.createElement( 'input' );
        search.type = 'text';
        search.className = 'filter-combobox-search';
        search.setAttribute( 'placeholder', 'Rechercher…' );
        search.setAttribute( 'aria-label', 'Rechercher — ' + placeholder.textContent );

        var list = document.createElement( 'ul' );
        list.className = 'filter-combobox-list';
        list.setAttribute( 'role', 'listbox' );

        panel.appendChild( search );
        panel.appendChild( list );
        wrapper.appendChild( trigger );
        wrapper.appendChild( panel );

        select.insertAdjacentElement( 'afterend', wrapper );
        select.hidden = true;

        var highlightedIndex = -1;
        var visibleItems     = []; // <li> elements currently rendered
        var resetLabel       = buildResetLabel( placeholder.textContent );

        /** (Re)render the option list, filtered by the search query. */
        function renderList( query ) {
            list.innerHTML = '';
            visibleItems  = [];
            highlightedIndex = -1;

            // Reset entry ("Tous les thèmes"…): pinned, unfiltered, visually
            // separated from real options (see .filter-combobox-option--reset).
            renderResetOption();

            var normalizedQuery = normalize( query || '' );

            options.slice( 1 ).forEach( function ( option ) {
                if ( ! normalizedQuery || normalize( option.textContent ).indexOf( normalizedQuery ) !== -1 ) {
                    visibleItems.push( renderOption( option ) );
                }
            });

            if ( visibleItems.length === 1 && normalizedQuery ) {
                var empty = document.createElement( 'li' );
                empty.className = 'filter-combobox-empty';
                empty.textContent = 'Aucun résultat';
                list.appendChild( empty );
            }
        }

        /** Render the pinned "Tous les X" entry, bound to the original placeholder <option>. */
        function renderResetOption() {
            var li = document.createElement( 'li' );
            li.className = 'filter-combobox-option filter-combobox-option--reset';
            li.setAttribute( 'role', 'option' );
            li.setAttribute( 'aria-selected', String( select.value === placeholder.value ) );
            li.textContent = resetLabel;
            li.dataset.value = placeholder.value;

            li.addEventListener( 'click', function () {
                selectOption( placeholder );
            });

            list.appendChild( li );
            visibleItems.push( li );
        }

        /** Create and append one <li role="option"> bound to a <option>. */
        function renderOption( option ) {
            var li = document.createElement( 'li' );
            li.className = 'filter-combobox-option';
            li.setAttribute( 'role', 'option' );
            li.setAttribute( 'aria-selected', String( select.value === option.value ) );
            li.textContent = option.textContent;
            li.dataset.value = option.value;

            li.addEventListener( 'click', function () {
                selectOption( option );
            });

            list.appendChild( li );
            return li;
        }

        /** Apply an option to the underlying select and notify filter-chroniques.js. */
        function selectOption( option ) {
            select.value = option.value;
            select.dispatchEvent( new Event( 'change' ) );

            trigger.textContent = option.textContent;
            trigger.classList.toggle( 'is-active', option.value !== placeholder.value );

            closePanel();
            trigger.focus();
        }

        function highlight( index ) {
            visibleItems.forEach( function ( li ) {
                li.classList.remove( 'is-highlighted' );
            });
            if ( index >= 0 && index < visibleItems.length ) {
                highlightedIndex = index;
                visibleItems[ index ].classList.add( 'is-highlighted' );
                visibleItems[ index ].scrollIntoView( { block: 'nearest' } );
            }
        }

        function openPanel() {
            renderList( '' );
            panel.hidden = false;
            wrapper.classList.add( 'is-open' );
            trigger.setAttribute( 'aria-expanded', 'true' );
            search.value = '';
            search.focus();
        }

        function closePanel() {
            panel.hidden = true;
            wrapper.classList.remove( 'is-open' );
            trigger.setAttribute( 'aria-expanded', 'false' );
        }

        function isOpen() {
            return ! panel.hidden;
        }

        // ── Events ──

        trigger.addEventListener( 'click', function () {
            if ( isOpen() ) {
                closePanel();
            } else {
                openPanel();
            }
        });

        search.addEventListener( 'input', function () {
            renderList( search.value );
        });

        search.addEventListener( 'keydown', function ( e ) {
            switch ( e.key ) {
                case 'ArrowDown':
                    e.preventDefault();
                    highlight( Math.min( highlightedIndex + 1, visibleItems.length - 1 ) );
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    highlight( Math.max( highlightedIndex - 1, 0 ) );
                    break;
                case 'Enter':
                    e.preventDefault();
                    if ( highlightedIndex >= 0 && visibleItems[ highlightedIndex ] ) {
                        var value = visibleItems[ highlightedIndex ].dataset.value;
                        var match = options.filter( function ( o ) { return o.value === value; } )[0];
                        if ( match ) {
                            selectOption( match );
                        }
                    }
                    break;
                case 'Escape':
                    e.preventDefault();
                    closePanel();
                    trigger.focus();
                    break;
            }
        });

        document.addEventListener( 'click', function ( e ) {
            if ( isOpen() && ! wrapper.contains( e.target ) ) {
                closePanel();
            }
        });
    }
});
