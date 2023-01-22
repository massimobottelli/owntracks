<?php
error_reporting(E_ALL);

include 'config.php';

if (!isset($_POST["view"])) {
    $view = "owntracks";
} else {
    $view = $_POST["view"];
}

try {
    $conn = new mysqli(SERVER, USERNAME, PASSWORD, DBNAME);
    if ($conn->connect_error) {
        throw new Exception("Cannot connect to database: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

$query = "SELECT timestamp, lat, lon FROM postrack";
if ($view == "owntracks") {
    if (isset($_POST["date"]) && !empty($_POST["date"])) {
        $date = $_POST["date"];
    } else {
        $date = date("Y-m-d");
    }
    $query .= " WHERE timestamp LIKE '%$date%'";
}

$result = mysqli_query($conn, $query);
$k = 0;
while ($r = mysqli_fetch_array($result)) {
    $timestamp[$k] = $r["timestamp"];
    $lat[$k] = $r["lat"];
    $lon[$k] = $r["lon"];
    $k++;
}
mysqli_close($conn);
?>

<html>
  <head>
    <title>Owntracks</title>
    <link rel="apple-touch-icon" href="src/owntracks.png" sizes="120x120">
    <link rel="shortcut icon" href="src/owntracks.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="src/my-style.css" />
    <link rel="stylesheet" href="src/leaflet.css" />
    <script src="src/heatmap.js"></script>
    <script src="src/leaflet.js"></script>
    <script src="src/leaflet-heat.js"></script>
    <script src="src/my-functions.js"></script>
  </head>
  <body>
    <div class="container">
      <form>
        <input type="hidden" name="view" />
        <?php if ($view != "owntracks") { ?>
        <input type="button" value="Owntracks" class="button" onclick="reloadPage('owntracks')">
        <?php } else { ?>
        <input type="button" value="Heatmap" class="button" onclick="reloadPage('heatmap')">
  <?php } ?>
      </form>
      <form name="datepicker" class="datepicker" method="POST"> 
        <input type="button" value="&lt;&lt;" class="button" onclick="previousDay()">
        <input type="text" id="datepicker" name="date" 
          value="<?php if(isset($date)){ echo $date;} ?>" onchange="submitForm()">
        <input type="button" value="&gt;&gt;" class="button" onclick="nextDay()">
      </form>
    </div>
    <div id="map"></div>
    <script>
      var view = <?php echo json_encode($view); ?>;

      var lat = <?php echo json_encode($lat); ?>,numbers = lat.map(Number);
      var lon = <?php echo json_encode($lon); ?>,numbers = lon.map(Number);
      var timestamp = <?php echo json_encode($timestamp); ?>;
      
      // prepare latlngs array
      var latlngs = [];
      for(var i = 0; i < lat.length; i++){
        var point = [lat[i],lon[i]];
        latlngs.push(point);
        }         
      
      // show OSM map
      var mymap = L.map('map', {
        center: [45.55, 9.12],
        zoom: 12
        });
      L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18}).addTo(mymap);

      if (view == "owntracks") {

        // show markers
        var marker = L.icon({
        iconUrl: 'src/marker.png',
        iconSize: [25, 25],
        });
        for(var i = 0; i < lat.length; i++){
        L.marker([lat[i],lon[i]],{icon: marker}).
        addTo(mymap).bindPopup(timestamp[i])
        }

        // show polyline
        var polyline = L.polyline(latlngs, {color: 'red'}).addTo(mymap);

        // adapt zoom to polylline
        mymap.fitBounds(polyline.getBounds(), { padding: [5,5] });
      
      }
      else {

        // show heatmap
        var heat = L.heatLayer(latlngs, {
            radius: 25,
            blur: 15
          }).addTo(mymap);
        
        // adapt zoom to heatmap
        var latlngsBounds = latlngs.reduce(function(bounds, latlng) {
           return bounds.extend(latlng);
        }, new L.LatLngBounds());
        mymap.fitBounds(latlngsBounds, { padding: [5,5] });
      }
      </script>
  </body>
</html>