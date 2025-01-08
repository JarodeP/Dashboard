
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style/test.css">
    <link rel="icon" type="image/x-icon" href="images/Logotest.png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
   
        function drawHistogram() {
            var data = google.visualization.arrayToDataTable([
                ['Zone', 'Nombre d\'accidents'],
                <?php
                include("Connexion.php");
                $req2 = $db->query("SELECT COUNT(*) as nombre_accidents, sae303_accident.agg 
                                    FROM sae303_accident 
                                    JOIN sae303_commune ON sae303_accident.com = sae303_commune.com_id 
                                    WHERE sae303_commune.DEP = '46' 
                                    GROUP BY sae303_accident.agg;");
                $data2 = $req2->fetchAll(PDO::FETCH_ASSOC);

                foreach ($data2 as $row2) {
                    $zone = ($row2['agg'] == 2) ? "En agglomération" : "Hors agglomération";
                    echo "['$zone', " . $row2['nombre_accidents'] . "],";
                }

                $req2->closeCursor();
                ?>
            ]);

            var options = {
                title: 'Nombre d\'accidents en fonction de la zone',
                hAxis: { title: 'Zone' },
                vAxis: { title: 'Nombre d\'accidents', minValue: 0 },
                legend: 'none'
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
        function drawLineChart() {
            var data = google.visualization.arrayToDataTable([
            ['Condition Météorologique', 'Nombre d\'accidents'],
            <?php
            include("Connexion.php");
            $req2 = $db->query("SELECT COUNT(*) as nombre_accidents, sae303_accident.atm 
                        FROM sae303_accident 
                        JOIN sae303_commune ON sae303_accident.com = sae303_commune.com_id 
                        WHERE sae303_commune.DEP = '46' 
                        GROUP BY sae303_accident.atm;");
            $data2 = $req2->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($data2 as $row2) {
                $condition = '';
                switch ($row2['atm']) {
                case 1:
                    $condition = "Soleil";
                    break;
                case 2:
                    $condition = "Pluie";
                    break;
                case 3:
                    $condition = "Neige";
                    break;
                case 4:
                    $condition = "Brouillard";
                    break;
                case 5:
                    $condition = "Vent";
                    break;
                case 6:
                    $condition = "Tempête";
                    break;
                case 7:
                    $condition = "Temps Éblouissant";
                    break;
                case 8:
                    $condition = "Temps couvert";
                    break;
                case 9:
                    $condition = "Autres";
                    break;
                default:
                    $condition = "Autres";
                }
                echo "['$condition', " . $row2['nombre_accidents'] . "],";
            }
        
            $req2->closeCursor();
            ?>
            ]);
        
            var options = { title: 'Conditions Météorologiques au moment de l\'accident' };
            var chart = new google.visualization.LineChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }

        function drawBarChart() {
            var data = google.visualization.arrayToDataTable([
                ['Type de Véhicule', 'Nombre d\'accidents'],
                <?php
                include("Connexion.php");
                $vehicleNames = [
                    "0" => "Indéterminable", "1" => "Bicyclette", "2" => "Cyclomoteur",
                    "3" => "Voiturette", "7" => "VL seul", "10" => "VU seul 1,5T <= PTAC <= 3,5T",
                    "13" => "PL seul 3,5T <PTCA <= 7,5T", "14" => "PL seul > 7,5T",
                    "15" => "PL > 3,5T + remorque", "16" => "Tracteur routier seul",
                    "17" => "Tracteur routier + semi-remorque", "20" => "Engin spécial",
                    "21" => "Tracteur agricole", "30" => "Scooter < 50 cm3",
                    "31" => "Motocyclette > 50 cm3 et <= 125 cm3", "32" => "Scooter > 50 cm3 et <= 125 cm3",
                    "33" => "Motocyclette", "34" => "Scooter > 125 cm3", "35" => "Quad léger <= 50 cm3",
                    "36" => "Quad lourd > 50 cm3", "37" => "Autobus", "38" => "Autocar", "39" => "Train", "40" => "Tramway",
                    "41" => "3RM <= 50 cm3", "42" => "3RM > 50 cm3 <= 125 cm3", "43" => "3RM > 125 cm3",
                    "50" => "EDP à moteur", "60" => "EDP sans moteur", "80" => "VAE", "99" => "Autre véhicule"
                ];
                $req3 = $db->query("SELECT COUNT(sae303_usager.Catu) AS nombre_accidents, sae303_vehicule.catv
                                    FROM sae303_usager
                                    INNER JOIN sae303_vehicule ON sae303_usager.Num_Acc = sae303_vehicule.Num_Acc
                                    INNER JOIN sae303_accident ON sae303_usager.Num_Acc = sae303_accident.Accident_id
                                    INNER JOIN sae303_commune ON sae303_accident.com = sae303_commune.com_id
                                    WHERE sae303_usager.grav = '2' AND sae303_commune.DEP = '46'
                                    GROUP BY sae303_vehicule.catv;");
                $data3 = $req3->fetchAll(PDO::FETCH_ASSOC);

                foreach ($data3 as $row3) {
                    $catv = $row3['catv'];
                    $vehicleName = isset($vehicleNames[$catv]) ? $vehicleNames[$catv] : "Inconnu";
                    echo "['$vehicleName', " . $row3['nombre_accidents'] . "],";
                }

                $req3->closeCursor();
                ?>
            ]);

            var options = {
                title: 'Accidents mortels par type de véhicule',
                hAxis: { title: 'Nombre d\'accidents' },
                vAxis: { title: 'Type de Véhicule' },
                chartArea: { width: '80%', height: '80%' },
                bars: 'horizontal'
            };

            var chart = new google.visualization.BarChart(document.getElementById('barchart'));
            chart.draw(data, options);
        }

        function drawDonutChart() {
    var data = google.visualization.arrayToDataTable([
        ['Moment', 'Nombre d\'accidents'],
        <?php
        include("Connexion.php");
        $req4 = $db->query("SELECT sae303_accident.lum, COUNT(*) as nombre_accidents 
                            FROM sae303_accident 
                            JOIN sae303_commune ON sae303_accident.com = sae303_commune.com_id 
                            WHERE sae303_commune.DEP = '46' 
                            GROUP BY sae303_accident.lum;");
        $data4 = $req4->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data4 as $row4) {
            switch ($row4['lum']) {
                case 1:
                    $moment = "Plein jour";
                    break;
                case 2:
                    $moment = "Crépuscule ou aube";
                    break;
                case 3:
                    $moment = "Nuit sans éclairage public";
                    break;
                case 4:
                    $moment = "Nuit avec éclairage non allumé";
                    break;
                case 5:
                    $moment = "Nuit avec éclairage allumé";
                    break;
                default:
                    $moment = "Autres";
                    break;
            }
            echo "['$moment', " . $row4['nombre_accidents'] . "],";
        }

        $req4->closeCursor();
        ?>
    ]);

    var options = {
        title: 'Conditions d’éclairage lors des accidents',
        pieHole: 0.4
    };

    var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
    chart.draw(data, options);
}


        google.charts.setOnLoadCallback(function() {
            drawHistogram();
            drawLineChart();
            drawBarChart();
            drawDonutChart();
        });

        window.onresize = function() {
            drawHistogram();
            drawLineChart();
            drawBarChart();
            drawDonutChart();
        };
    </script>

    <?php
include("Connexion.php");

// Calculer le nombre total d'accidents
$req = $db->query("SELECT COUNT(*) as nombre_accidents FROM sae303_accident 
                   JOIN sae303_commune ON sae303_accident.com = sae303_commune.com_id 
                   WHERE sae303_commune.DEP = '46'");
$data = $req->fetch(PDO::FETCH_ASSOC);
$nombre_accidents = $data['nombre_accidents']; // Stocke le résultat dans la variable
$req->closeCursor();

$nombre_habitants = 174094; // Remplacez par la valeur réelle
// Calcul du taux d'accidents pour 10 000 habitants
$taux_accidents = ($nombre_accidents / $nombre_habitants) * 100;
?>

<?php
///////////////////////////Carte////////////////////////
include("Connexion.php");
try {
    // Récupération des données des accidents
    $sqlAccidents = "SELECT lat, `long`, COUNT(Accident_Id) AS nombre_accidents
                     FROM sae303_accident 
                     WHERE com IN (SELECT com_id FROM sae303_commune) 
                     GROUP BY lat, `long` 
                     HAVING COUNT(Accident_Id) > 0";
    $stmtAccidents = $db->query($sqlAccidents);

    $accidentData = [];
    while ($row = $stmtAccidents->fetch(PDO::FETCH_ASSOC)) {
        $accidentData[] = [
            'latitude' => (float)str_replace(',', '.', $row['lat']),
            'longitude' => (float)str_replace(',', '.', $row['long']),
            'nombre_accidents' => (int)$row['nombre_accidents']
        ];
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>



</head>
<body>

<header>
    <div class="logo"><img src="images/Logo.png" alt="Logo"></div>
    
    <!------------------Barre de recherche----------------------->
    <div class="search-container">
  <input type="text" class="search-bar" placeholder="Barre de recherche">
  <div class="search-results" id="search-results">
    <div class="option" data-destination="carte">Carte interactive</div>
    <div class="option" data-destination="graph">Graphique principaux</div>
    <div class="option" data-destination="graph2">Graphique dynamique</div>
    <!-- Ajouter d'autres options ici avec data-destination approprié -->
  </div>
</div>


<div class="pdp-button"><img src="images/pdp.png" alt="Image de profil"></div>
<button class="login-button" onclick="showAlert()">Se connecter</button>

<script>
function showAlert() {
    alert("Cette fonctionnalité n'est pas encore disponible.");
}
</script>
</header>
<div class="sidebar">
    <ul>
        <li><a href="#" id="plus">Graphique principaux</a></li>
        <li><a href="#" id="interactif">Carte interactive</a></li>
        <li><a href="#" id="graph-dyna">Graphique dynamique</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="data">
        <div class="data-item">174 094 habitants</div>
        <div class="data-item"><?php echo $nombre_accidents; ?> accidents</div>
        <div class="data-item"><?php echo round($taux_accidents, 2);?>% accidents par habitant</div>
    </div>
<div class="graphiques">
    <div class="graphs">
        <div class="graph" id="graph1"><div id="chart_div" class="chart-container"></div></div>
        <div class="graph" id="graph2"><div id="piechart" class="chart-container"></div></div>
        <div class="graph" id="graph3"><div id="barchart" class="chart-container"></div></div>
        <div class="graph" id="graph4"><div id="donutchart" class="chart-container"></div></div>
    </div>
    <br>
    <div class="extra-text">
    Ce tableau de bord affiche les données actualisées sur les accidents dans le département du Lot (46). À travers quatre graphiques et indicateurs.
    </div>
</div><!--Fermeture de la div graphique pour l'effacer-->

    <div class="carte">
        <h1>Carte des accidents du Lot(46)</h1>
        <div id="map"></div>
        <div class="extra-text">
        <p>Voici une carte interactive dans laquel vous pouvez vous déplacer au sein du département pour y voir ou les accidents ont eu lieu</p>
    </div>
    </div>
    
<!--------------------------------------------------------------------Le code pour le graphique interactif : -------------------------------------->
<div class="graph-interact hidden">
    <h1>Graphique dynamique</h1>
    <!-- Buttons to modify the chart data -->
    <div id="button-container">
        <button onclick="updateChart(1)">Indemne</button>
        <button onclick="updateChart(2)">Tué</button>
        <button onclick="updateChart(3)">Blessé hospitalisé</button>
        <button onclick="updateChart(4)">Blessé léger</button>
    </div>

    <!-- Div for the dynamic chart -->
    <div id="chart_div_dynamic" class="chart-container hidden"></div>

    <script type="text/javascript">
        var chartData = {};
        var chartOptions = {
            title: 'Nombre d\'usagers en fonction de la place',
            hAxis: { title: 'Place' },
            vAxis: { title: 'Nombre d\'usagers' },
            legend: 'none'
        };
        var chart;
        var currentGravite = null;

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(function() {
            chart = new google.visualization.ColumnChart(document.getElementById('chart_div_dynamic'));
        });

        function updateChart(gravite) {
            currentGravite = gravite;
            var dataArray = [['Place', 'Nombre d\'usagers']];
            var data = chartData[gravite];
            if (data) {
                data.forEach(function(row) {
                    dataArray.push(['Place ' + row.Place, row.Nombre]);
                });
                var dataTable = google.visualization.arrayToDataTable(dataArray);
                chart.draw(dataTable, chartOptions);
                document.getElementById('chart_div_dynamic').classList.remove('hidden');
                $('.sidebar').animate({ height: '132vh' }, 500); // Changement de hauteur en 500 ms
            }
        }

        <?php
        include("Connexion.php");
        $sql = "SELECT u.grav AS Gravite, u.place AS Place, COUNT(*) AS Nombre FROM sae303_usager u JOIN sae303_accident a ON u.Num_Acc = a.Accident_Id GROUP BY u.grav, u.place ORDER BY u.grav, u.place";
        $result = $db->query($sql);
        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        $chartData = [];
        foreach ($data as $row) {
            $gravite = $row['Gravite'];
            $place = $row['Place'];
            $nombre = (int)$row['Nombre'];
            if (!isset($chartData[$gravite])) {
                $chartData[$gravite] = [];
            }
            $chartData[$gravite][] = ['Place' => $place, 'Nombre' => $nombre];
        }

        echo "var chartData = " . json_encode($chartData) . ";";
        ?>

        window.onresize = function() {
            if (currentGravite !== null) {
                updateChart(currentGravite);
            }
        };
    </script>
<img src="./images/accident-legend.jpg" alt="">

    <div class="extra-text">
        <p>Voici un graphique interactif vous permettant de changer les valeurs de celui-ci en temps réel</p>
    </div>
</div>
</div> <!--Fermeture du main content-->
<br>

<div class="footer-container">
    <footer>
        <p>&copy; Jarode Pottin - 2024</p>
    </footer>
</div>
<!--------------------------------------------------------------------Le code pour la carte : -------------------------------------->


<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
       var map = L.map('map').setView([44.4667, 1.4333], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);

       
        // Afficher le contour de la commune (si disponible)
        var Lot= {
  "type": "Feature",
  "geometry": {
    "type": "Polygon",
    "coordinates": [
      [
        [1.4482602497483, 45.019314041206],
        [1.4619826792135, 45.01370224029],
        [1.4735839270693, 45.017999050409],
        [1.4800566881166, 45.026797710108],
        [1.502854716064, 45.038440870886],
        [1.519580065039, 45.041030205266],
        [1.5357229531152, 45.046275852749],
        [1.5410691079459, 45.042887041934],
        [1.5437040262063, 45.030761580224],
        [1.5520446044451, 45.028473138039],
        [1.5690421523205, 45.038712199205],
        [1.5763101796546, 45.040696470827],
        [1.5893103965749, 45.036412402334],
        [1.614739866204, 45.033019699506],
        [1.629956153879, 45.033567269253],
        [1.6509774862668, 45.025013308842],
        [1.6544545546621, 45.017019229934],
        [1.6714097923126, 45.004301541515],
        [1.6843750926429, 45.002660865737],
        [1.6871611264702, 44.996380368243],
        [1.702662490825, 44.987825577102],
        [1.7110326508746, 44.967296262803],
        [1.7215459564475, 44.968065955731],
        [1.7428395473269, 44.959830662128],
        [1.7506187292807, 44.954961208829],
        [1.7536701932124, 44.940576173829],
        [1.7685216398013, 44.93111838651],
        [1.7749276020491, 44.923721627249],
        [1.7828574905171, 44.929652757246],
        [1.78487170617, 44.937317933441],
        [1.8008914101993, 44.924210099081],
        [1.8085816314758, 44.927710042504],
        [1.8239145074647, 44.927683348732],
        [1.836439246631, 44.937455442607],
        [1.8443484053735, 44.938030495187],
        [1.8510527260352, 44.946245504656],
        [1.8670322636063, 44.952926075187],
        [1.8874095971943, 44.956563455989],
        [1.8926655050781, 44.964882977079],
        [1.9081575006047, 44.978423183181],
        [1.9280650601996, 44.97871493763],
        [1.9390669995572, 44.973222308505],
        [1.9407166400253, 44.95513995704],
        [1.9509915581584, 44.953151935667],
        [1.9557698918802, 44.958318624648],
        [1.9740981161193, 44.966632803845],
        [1.9853569537575, 44.974450164818],
        [2.0068687915517, 44.976629769782],
        [2.0454327798436, 44.983664804729],
        [2.0525667664657, 44.976478188068],
        [2.0629079799591, 44.9765045515],
        [2.0806940542158, 44.953285983552],
        [2.0763195910268, 44.940561879615],
        [2.0786238601329, 44.932494333995],
        [2.0892278644094, 44.928560463933],
        [2.1081047030139, 44.910638688547],
        [2.0864901589829, 44.901079919432],
        [2.0837359232769, 44.887424173789],
        [2.0942097640089, 44.872012369152],
        [2.1165704984568, 44.850184202716],
        [2.1227770243941, 44.847633102732],
        [2.1398031019064, 44.823820670465],
        [2.1658208187006, 44.812904212621],
        [2.1652929430823, 44.799560342182],
        [2.1716374993124, 44.790027108976],
        [2.1667022088341, 44.772651831571],
        [2.1493997719958, 44.769790512407],
        [2.1534957604021, 44.753104006989],
        [2.1522238006118, 44.736723151663],
        [2.1479681938401, 44.723035803571],
        [2.1336852541707, 44.70956475715],
        [2.1301318075949, 44.698486086307],
        [2.1386637746726, 44.692880659597],
        [2.1552964923022, 44.70023944349],
        [2.1630322730753, 44.690215331586],
        [2.1791523780579, 44.674446124105],
        [2.1655660362222, 44.661375303382],
        [2.1740442827509, 44.653644834741],
        [2.1687595856936, 44.647386736712],
        [2.1694164789558, 44.638069782843],
        [2.1908059572226, 44.628252004845],
        [2.2074728028173, 44.61552895784],
        [2.2108934010391, 44.606090357773],
        [2.1954101381218, 44.600778595174],
        [2.2017928242867, 44.596049696004],
        [2.1936051879406, 44.58591143442],
        [2.1844862284485, 44.59021904525],
        [2.1676095238949, 44.590329078654],
        [2.1538128201085, 44.57189782658],
        [2.1346205537833, 44.569849513532],
        [2.124513418981, 44.576347447406],
        [2.1104955943486, 44.571376544464],
        [2.1015590079994, 44.572436798295],
        [2.0830462458941, 44.587423324047],
        [2.0728206231132, 44.577955873938],
        [2.0573689651355, 44.582909160412],
        [2.0335854594418, 44.56881637632],
        [2.0340696763027, 44.560760159834],
        [2.0203127144902, 44.555504531802],
        [2.0050084589377, 44.557033268559],
        [1.9885549262677, 44.551182134401],
        [1.9751753123345, 44.537145405011],
        [1.970563904818, 44.528891720337],
        [1.9562278200819, 44.518455813836],
        [1.9366297490837, 44.515148261474],
        [1.9314639777047, 44.506396941037],
        [1.9129865180384, 44.505157692579],
        [1.9115825451404, 44.499801458356],
        [1.9191710997594, 44.49312202364],
        [1.9098594387895, 44.48674631631],
        [1.9061154790499, 44.499870727249],
        [1.8902857028608, 44.505886029507],
        [1.8841752229262, 44.498432886597],
        [1.885217137804, 44.486107475987],
        [1.8799586666095, 44.483837662409],
        [1.8615767786303, 44.487320895101],
        [1.843001484365, 44.482382092855],
        [1.8396044963184, 44.475903822342],
        [1.8470020898658, 44.470160068683],
        [1.8526845623673, 44.459216811677],
        [1.846648081567, 44.444555574418],
        [1.8505242685611, 44.43550406388],
        [1.8731907138082, 44.424140758608],
        [1.8710314852874, 44.397745172704],
        [1.8744004614259, 44.391430087278],
        [1.8907941991097, 44.380202764676],
        [1.8933692462979, 44.371571831531],
        [1.9068476903719, 44.364871814968],
        [1.9112348353677, 44.356651948333],
        [1.9052533999665, 44.350549277067],
        [1.8907241899993, 44.351737032539],
        [1.8820777699351, 44.340068798134],
        [1.8556031233321, 44.332582043853],
        [1.8456122621165, 44.33613444695],
        [1.8332009402511, 44.335457059576],
        [1.8270255325815, 44.32426884987],
        [1.8094302106683, 44.329086072158],
        [1.8059777797489, 44.336632362484],
        [1.7833453885707, 44.327943604564],
        [1.7873478934768, 44.323581975324],
        [1.7819129603983, 44.314472615323],
        [1.768544261781, 44.314939492971],
        [1.7553567443039, 44.325817340987],
        [1.7373556664188, 44.326979663175],
        [1.7318042883639, 44.317812372941],
        [1.7151285678926, 44.313035587615],
        [1.6994966069311, 44.313816007339],
        [1.6790543823345, 44.300601364801],
        [1.6628876357397, 44.292814929567],
        [1.6466377173529, 44.294790204319],
        [1.6370645866245, 44.298462052118],
        [1.6319878111146, 44.293887757522],
        [1.6516181749095, 44.286809376053],
        [1.6422766943958, 44.270771530189],
        [1.6332195435462, 44.269409693186],
        [1.6156752023963, 44.278396127792],
        [1.6169053391703, 44.297530575144],
        [1.6055857426408, 44.295656355547],
        [1.5932068472358, 44.302911512641],
        [1.5872383982734, 44.298779228534],
        [1.577057905448, 44.30164046075],
        [1.569137976709, 44.298392987101],
        [1.5777775117549, 44.284496801325],
        [1.5624962356347, 44.27966878594],
        [1.5632414387973, 44.274886379262],
        [1.5770804296349, 44.270221202549],
        [1.5800311670669, 44.260206126797],
        [1.5872487604831, 44.249006343623],
        [1.5752287750623, 44.238734921684],
        [1.5417602550587, 44.227813925717],
        [1.5290844067842, 44.23537357693],
        [1.518246985951, 44.249518712925],
        [1.5205325763103, 44.263266971406],
        [1.5087806340611, 44.273638671169],
        [1.4951437462566, 44.271095820859],
        [1.4732171576142, 44.284206796054],
        [1.4618426585284, 44.26737925836],
        [1.4531793595742, 44.267176611688],
        [1.4535590038918, 44.254122364815],
        [1.4392544450401, 44.250013706447],
        [1.4309892807553, 44.243934249729],
        [1.4223000036257, 44.242769039907],
        [1.3803669452448, 44.224855014913],
        [1.3567965742905, 44.204018679795],
        [1.3363963237902, 44.228164291061],
        [1.3205552714658, 44.232050137303],
        [1.3068776511303, 44.226976620673],
        [1.2813755448768, 44.235788833792],
        [1.2862503206206, 44.242724546526],
        [1.2842803990087, 44.251751936818],
        [1.3040561797661, 44.262956553109],
        [1.2942283079115, 44.269931405949],
        [1.3036419786638, 44.293780222919],
        [1.2984232978299, 44.294998753146],
        [1.2826505678741, 44.290126601767],
        [1.2717164107238, 44.281701772898],
        [1.2554819885111, 44.285553546834],
        [1.2500166763573, 44.272900537029],
        [1.2251735135029, 44.27952570615],
        [1.2170572336728, 44.276902146179],
        [1.2046104960799, 44.282089030781],
        [1.1845970264102, 44.286843708109],
        [1.177527462072, 44.292155931446],
        [1.1812474366675, 44.30737621701],
        [1.1684135504581, 44.305176929208],
        [1.1593432965978, 44.310622218703],
        [1.1460615025306, 44.308815289657],
        [1.1359881581847, 44.317222086283],
        [1.1252404136057, 44.315439371114],
        [1.1104058105295, 44.323116166197],
        [1.1079322101063, 44.327405475254],
        [1.11341021313, 44.338851885589],
        [1.1032236287631, 44.346211355086],
        [1.0897034112701, 44.347776457751],
        [1.0812215694247, 44.354388321287],
        [1.0912647526615, 44.359943917324],
        [1.0935259886791, 44.365959224021],
        [1.1071599922422, 44.366598851034],
        [1.110748036109, 44.370191952886],
        [1.1274389106322, 44.372985753369],
        [1.1347841409898, 44.379001901251],
        [1.1325463304764, 44.393331095059],
        [1.113581510295, 44.391324005037],
        [1.1035062544519, 44.392306175475],
        [1.0824000913579, 44.381409106067],
        [1.0640814762214, 44.378508721439],
        [1.060916410464, 44.388148956247],
        [1.0514192493927, 44.392094511982],
        [1.0613076444275, 44.401878633002],
        [1.060811266939, 44.416585968472],
        [1.0574845114814, 44.427673212448],
        [1.0452829749205, 44.434328842652],
        [1.0333318174167, 44.432217983065],
        [1.0247167778907, 44.442988405687],
        [1.0209838507254, 44.456237710915],
        [1.0238896841162, 44.464106031022],
        [1.0230093439874, 44.475437273235],
        [1.009020244264, 44.480044617987],
        [1.0168410499751, 44.492627682886],
        [1.0162275856839, 44.505873805304],
        [0.99607013748509, 44.526838106147],
        [0.98177646477517, 44.543949619625],
        [0.99391928143422, 44.549540779914],
        [1.0102474849887, 44.545187788418],
        [1.0131652927005, 44.53612981948],
        [1.0347007735952, 44.555410813413],
        [1.0463221422077, 44.562091748647],
        [1.0716939661813, 44.567841711875],
        [1.0751420531924, 44.577325705506],
        [1.0915723340934, 44.57129825478],
        [1.10321434571, 44.571734741055],
        [1.1023480014998, 44.583112143598],
        [1.0954254371464, 44.590239410789],
        [1.1076423221767, 44.604047948788],
        [1.137389330558, 44.623916068209],
        [1.1537948801342, 44.639408990845],
        [1.1467257654642, 44.651942998392],
        [1.1466756285759, 44.670346129862],
        [1.1631824196253, 44.674246913408],
        [1.1691224492748, 44.680201458326],
        [1.1814922279959, 44.68312050249],
        [1.1922298707031, 44.682144673083],
        [1.2245513454404, 44.684265469136],
        [1.2404413959773, 44.692803925964],
        [1.2433609148288, 44.703747365953],
        [1.2482656593079, 44.707708989465],
        [1.2637980717141, 44.710685806718],
        [1.2704127081354, 44.722361774434],
        [1.2877769806729, 44.714784618791],
        [1.2997473700748, 44.733876961929],
        [1.3005234409238, 44.743067753612],
        [1.3160471856435, 44.740370070289],
        [1.322816677032, 44.765133167519],
        [1.313412866091, 44.766040449212],
        [1.2962430489699, 44.777811462978],
        [1.3042790767232, 44.788545534304],
        [1.2996402457488, 44.796921415066],
        [1.3281059944688, 44.806531488239],
        [1.3368655384896, 44.806071554052],
        [1.3641055003826, 44.811568223737],
        [1.3606303391154, 44.826748288149],
        [1.3614088604034, 44.840796241728],
        [1.3699846242068, 44.846449057072],
        [1.3771449543322, 44.84182444963],
        [1.3861021387482, 44.847434745656],
        [1.4019376918366, 44.849449622068],
        [1.4048297845553, 44.862526244834],
        [1.4181797898114, 44.870598618527],
        [1.4310840691639, 44.871280603185],
        [1.4419256468077, 44.877575693392],
        [1.4398567374008, 44.888947217313],
        [1.4216345306166, 44.896767467267],
        [1.4135387322774, 44.911821580701],
        [1.4246258313763, 44.919694190356],
        [1.4423501795316, 44.916547257696],
        [1.4365133670875, 44.93225069741],
        [1.4364044047008, 44.940615131261],
        [1.4207336396897, 44.955116554897],
        [1.414587951063, 44.977794038016],
        [1.4133042325955, 44.999381814036],
        [1.4092638730924, 45.006004469319],
        [1.4281821749303, 45.009219883472],
        [1.4482602497483, 45.019314041206]
      ]
    ]
  },
  "properties": {
    "code": "46",
    "nom": "Lot"
  }
};
L.geoJSON(Lot).addTo(map);

var accidentData = <?php echo json_encode($accidentData); ?>;

console.log("Données des accidents: ", accidentData);

accidentData.forEach(function(location) {
    console.log("Coordonnées: ", location.latitude, location.longitude, "Nombre d'accidents: ", location.nombre_accidents);
    var radius = location.nombre_accidents * 100;
    var circle = L.circle([location.latitude, location.longitude], {
        color: 'red',
        fillColor: '#ff0000',
        fillOpacity: 0.7,
        radius: radius,
        zindex:10,
    }).addTo(map)
    .bindPopup("Nombre d'accidents : " + location.nombre_accidents);
});

    </script>
    <!----------------------------------------------Fin de code pour la carte : -------------------------------------->

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="index.js"></script>
</body>
</html>