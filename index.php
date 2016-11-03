<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Lukkari</title>
      <style>
      body {
          font-family: Sans-Serif;
          background-color: #a63636;
          color: white;
       }
       hr {
           color: white;
       }
      </style>
  </head>
<body>
<?php

$data = unserialize(file_get_contents("data.array"));

$weekday = array(
    0 => "Maanantai",
    1 => "Tiistai",
    2 => "Keskiviikko",
    3 => "Torstai",
    4 => "Perjantai"
);

for ($i = 0; $i < 5; $i++) {
    echo "<h2>{$weekday[$i]}</h2>";
    foreach($data[$i] as $day) {
        echo "kurssi: {$day['name']}<br>";
        echo "Kurssi tunnus: {$day['courseid']}<br>";
        echo "Aika: {$day['time']}<br>";
        echo "Luokka: {$day['room']}<br>";
        echo"<br>";
    }
    echo "<hr>";
}
echo "<script>" . file_get_contents("snowstorm-min.js") . "</script>";
?>
    <script>
     snowStorm.followMouse = false;
     snowStorm.vMaxX = 3;
     snowStorm.vMaxY = 3;
    </script>
</body>
</html>
