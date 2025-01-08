$(document).ready(function() {
  let chart = null; // Variable pour stocker le graphique

  // Fonction pour créer un graphique
  function createDynamicChart() {
      const chartDiv = $("#chart_div_dynamic");
      chartDiv.removeClass('hidden'); // Rend le conteneur visible

      // Exemple de création avec Chart.js
      if (!chart) {
          chart = new Chart(chartDiv[0], {
              type: 'bar',
              data: {
                  labels: ['Jan', 'Feb', 'Mar'],
                  datasets: [{
                      label: 'Données exemple',
                      data: [10, 20, 30],
                      backgroundColor: 'rgba(54, 162, 235, 0.2)',
                      borderColor: 'rgba(54, 162, 235, 1)',
                      borderWidth: 1
                  }]
              },
              options: {
                  responsive: true
              }
          });
      }
  }

  // Rendre les graphiques visibles avec un fadeIn au chargement de la page
  $(".graphiques").css({ visibility: "visible", opacity: 0 }).animate({ opacity: 1 }, 1000);

  // Affichage des graphiques principaux
  $("#plus").click(function() {
      $(".graphiques").fadeIn(500).css({ visibility: "visible" }); // Apparition en 500 ms
      $(".carte, .graph-interact").fadeOut(500); // Disparition en 500 ms
      $('.sidebar').animate({ height: '170vh' }, 500); // Changement de hauteur en 500 ms
  });

  // Affichage de la carte
  $("#interactif").click(function() {
      $(".graphiques, .graph-interact").fadeOut(500); // Disparition en 500 ms
      $(".carte").fadeIn(500, function() {
          if (typeof map !== 'undefined') {
              map.invalidateSize(); // Redimensionnement de la carte après l'affichage
          }
      });
      $('.sidebar').animate({ height: '115vh' }, 500); // Changement de hauteur en 500 ms
  });

  // Affichage des graphiques dynamiques
  $("#graph-dyna").click(function() {
      $(".graphiques, .carte").fadeOut(500); // Disparition en 500 ms
      $(".graph-interact").fadeIn(500); // Apparition en 500 ms
      $('.sidebar').animate({ height: '132vh' }, 500); // Ajuste la hauteur
      createDynamicChart(); // Recrée un graphique dynamique
  });

  $("#button-container").click(function() {
      $('.sidebar').css('height', '130vh');
  });

  ////////////////////////////////////////LE FORMULAIRE ////////////////////////////////////////
  $('.search-bar').on('input', function() {
      const input = $(this).val().toLowerCase(); // Récupère et formate la saisie
      const $results = $('#search-results'); // Section des résultats

      if (input) {
          $results.show(); // Affiche les résultats
      } else {
          $results.hide(); // Cache les résultats si la barre est vide
      }

      $results.find('.option').each(function() {
          const text = $(this).text().toLowerCase(); // Texte de chaque option
          $(this).toggle(text.includes(input)); // Affiche/masque selon la recherche
      });
  });

  // Gestion des clics sur les options (avec délégation)
  $(document).on('click', '.option', function() {
      const destination = $(this).data('destination'); // Récupère la destination
      const $sidebar = $('.sidebar');

      if (destination === 'carte') {
          console.log("Navigation vers la carte interactive");
          $(".graphiques, .graph-interact").fadeOut(500); // Cache graphiques et formulaire
          $(".carte").fadeIn(500, function() {
              if (typeof map !== 'undefined') {
                  map.invalidateSize(); // Redimensionne la carte
              }
          });
          $sidebar.animate({ height: '115vh' }, 500); // Ajuste la hauteur
      } else if (destination === 'graph') {
          console.log("Navigation vers le graph supplémentaire");
          $(".graphiques").fadeIn(500).css({ visibility: "visible" }); // Affiche les graphiques
          $(".carte, .graph-interact").fadeOut(500); // Cache la carte et le formulaire
          $sidebar.animate({ height: '170vh' }, 500); // Ajuste la hauteur
      } else if (destination === 'graph2') {
          console.log("Navigation vers le graph dynamique");
          $(".graphiques, .carte").fadeOut(500); // Cache graphiques et carte
          $(".graph-interact").fadeIn(500); // Affiche le formulaire
          $sidebar.animate({ height: '130vh' }, 500); // Ajuste la hauteur
          createDynamicChart(); // Recrée un graphique dynamique
      } else {
          console.warn("Destination inconnue :", destination); // Gère les cas non prévus
      }

      $('.search-bar').val(''); // Réinitialise l'input
      $('#search-results').hide(); // Cache les résultats de recherche après sélection
  });
});
