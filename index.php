<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Lukkari</title>
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
echo"<h1>TTV15S3</h1>";
for ($i = 0; $i < 5; $i++) {
    echo "<hr><h2>{$weekday[$i]}</h2>";
    foreach($data[$i] as $day) {
        echo "kurssi: {$day['name']}<br>";
        echo "Kurssi tunnus: {$day['courseid']}<br>";
        echo "Aika: {$day['time']}<br>";
        echo "Luokka: {$day['room']}<br>";
        echo"<br>";
    }
}
?>

  </body>
</html>