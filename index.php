<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Jamk juttu</title>
  </head>
<body>
<?php
$file = file_get_contents("data.json");
$json = json_decode($file, true);

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
    //print_r($json[$i]);
    foreach($json[$i] as $day) {
        #print_r($day);
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