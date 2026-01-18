/**
 * Graphiques Chart.js pour les bilans trimestriels
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Vérifier que Chart.js est chargé
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n\'est pas chargé');
        return;
    }

    // Configuration générale
    const defaultColors = [
        '#38d6c2', '#2b4743', '#ea638c', '#efe4d5', '#811956',
        '#202c39', '#38d6e9', '#ea949c', '#4BC0C0', '#FF9F40'
    ];

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    };

    // Graphique Nationalités
    const nationalitesCtx = document.getElementById('chart-nationalites');
    if (nationalitesCtx && window.bilanData && window.bilanData.nationalites) {
        new Chart(nationalitesCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.bilanData.nationalites),
                datasets: [{
                    data: Object.values(window.bilanData.nationalites),
                    backgroundColor: defaultColors,
                }]
            },
            options: chartOptions
        });
    }

    // Graphique Genres
    const genresCtx = document.getElementById('chart-genres');
    if (genresCtx && window.bilanData && window.bilanData.genres) {
        new Chart(genresCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.bilanData.genres),
                datasets: [{
                    data: Object.values(window.bilanData.genres),
                    backgroundColor: defaultColors,
                }]
            },
            options: chartOptions
        });
    }

    // Graphique Parité
    const pariteCtx = document.getElementById('chart-parite');
    if (pariteCtx && window.bilanData && window.bilanData.auteurs) {
        new Chart(pariteCtx, {
            type: 'doughnut',
            data: {
                labels: ['Femmes', 'Hommes'],
                datasets: [{
                    data: [
                        window.bilanData.auteurs.femmes,
                        window.bilanData.auteurs.hommes
                    ],
                    backgroundColor: ['#38d6c2', '#ea638c'],
                }]
            },
            options: chartOptions
        });
    }
});