jQuery(document).ready(function($){
    // Trouve le textarea de l'extrait
    var $excerptField = $('#excerpt');

    if(!$excerptField.length) return;

    // Crée un compteur sous le textarea
    var $counter = $('<div id="excerpt-counter" style="margin-top:5px;font-size:0.9em;color:#555;"></div>');
    $excerptField.after($counter);

    function updateCounter() {
        var text = $excerptField.val();
        var wordCount = text.trim().split(/\s+/).filter(Boolean).length;
        var charCount = text.length;

        $counter.text('Mots: ' + wordCount + ' | Caractères: ' + charCount);
    }

    // Met à jour le compteur à l'ouverture et à chaque frappe
    updateCounter();
    $excerptField.on('input', updateCounter);
});
