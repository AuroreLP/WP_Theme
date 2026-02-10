<?php
// convertir les minutes en heure dans un format plus friendly pour durée du film
if (!function_exists('format_duree')) {
    function format_duree($minutes) {
        if ($minutes === '' || $minutes === null) return '';

        $minutes = (int) $minutes;

        $heures = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($heures > 0) {
            return $heures . 'h' . str_pad($mins, 2, '0', STR_PAD_LEFT);
        }

        return $mins . ' min';
    }
}