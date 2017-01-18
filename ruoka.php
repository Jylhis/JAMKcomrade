<html>
	<head>
    <meta charset="utf-8">
<title>Ruoka</title>
	</head>
<body>
	<?php 
	require_once('functions.php');
	
	date_default_timezone_set('Europe/Helsinki');
        if(isset($_GET['week']) && $_GET['week']>0 && $_GET['week']<=52) {
            //$week = $_GET['week'];
            $week = sprintf("%02d",$_GET['week']);
        } else {
            $week = date('W');
        }
        if(isset($_GET['year'])) {
            $year = $_GET['year'];
        } else {
            $year = date('Y');
        }
        /*if(isset($_GET['luokka'])) {
            $luokka = $_GET['luokka'];
        } else {
            $luokka = "TTV15S3";
        }*/

        $lastweek = $week-1;
        $nextweek = $week+1;
        $lastyear = $year-1;
        $nextyear = $year+1;

        echo "<h1>Aimo ";

        if($week == 1) {
            echo "<a href='{$_SERVER['PHP_SELF']}?week=52&year={$lastyear}'><<</a>";
        } else {
            echo "<a href='{$_SERVER['PHP_SELF']}?week={$lastweek}&year={$year}'><<</a>";
        }

        echo "Week: {$week}";

        if ($week == 52) {
            echo "<a href='{$_SERVER['PHP_SELF']}?week=1&year={$nextyear}'>>></a>";

        } else {
            echo "<a href='{$_SERVER['PHP_SELF']}?week={$nextweek}&year={$year}'>>></a>";
        }

        echo " Year: ".$year."</h1>";

// Check date
if ($year<date("Y")-1 || $year>date("Y")+1) {
    echo "Year must be between ".(date("Y")-1)."-".(date("Y")+1);
    return;
}
        $cacheFile = "cache/aimo-" . $week .'-'.$year;
        if(file_exists($cacheFile)) {
            echo file_get_contents($cacheFile);
        } else {
            echo GetAimo($week, $year);
            echo file_get_contents($cacheFile);
        }
    
	?>
</body>
</html>

<?php

function GetAimo() {
     $date = new DateTime();
    $date = $date->setISODate($year, $week)->format('Y/m/d');

    $url = "http://www.amica.fi/modules/json/json/Index?costNumber=0350&language=fi&firstDay={$date}";
    $html = file_get_contents($url);
    $json = json_decode($json);
    
    $days = $json['MenusForDays'];
    
    $weekOut = Array();
    
    foreach($days as $day) {
        $foods = $day['SetMenus'];
        
        $name = $foods->Name;
        $price = $foods->Price;
        
        $components = Array();
        
        foreach($foods->Components as $comp) {
            array_push($components, $comp);
        }
        
    }
    
    // Output
     if (!file_exists('cache')) {
            mkdir('cache', 0744, true);
        }

    if(empty($odata)) {
        file_put_contents('cache/aimo-'.$week .'-'. $year, "No data!");
        return;
    } else {

            $weekday = array(
                0 => "Maanantai",
                1 => "Tiistai",
                2 => "Keskiviikko",
                3 => "Torstai",
                4 => "Perjantai"
            );

            ob_start();
            for ($i = 0; $i < 5; $i++) {
                echo "<hr>";
                echo "<h2>{$weekday[$i]}</h2>";
                foreach($odata[$i] as $day) {
                    echo "Ruoka: {$day['name']}<br>";
                    echo "Hinta: {$day['courseid']}<br>";
                    echo "Ruokainekset: {$day['time']}<br>"; // TODO: LOOP
                    echo"<br>";
                }
            }
            file_put_contents('cache/aimo-'.$week .'-'. $year, ob_get_contents());
            ob_end_clean();
    }
}