<?php
/**
 * Formatting Helpers
 *
 * Utility functions for displaying data in human-friendly formats.
 * Used primarily in chronique sidebars and card components.
 *
 * @package turningpages
 */

/**
 * Convert a duration in minutes to a readable "Xh00" format.
 *
 * Used in chronique sidebars for film/series durations.
 *
 * Examples:
 *   tp_format_duree(104) → "1h44"
 *   tp_format_duree(45)  → "45 min"
 *   tp_format_duree(120) → "2h00"
 *   tp_format_duree('')  → ""
 *
 * @param  int|string|null $minutes  Duration in minutes.
 * @return string                    Formatted duration or empty string.
 */
function tp_format_duree( $minutes ) {
    if ( $minutes === '' || $minutes === null ) {
        return '';
    }

    $minutes = (int) $minutes;
    $heures  = floor( $minutes / 60 );
    $mins    = $minutes % 60;

    if ( $heures > 0 ) {
        return $heures . 'h' . str_pad( $mins, 2, '0', STR_PAD_LEFT );
    }

    return $mins . ' min';
}