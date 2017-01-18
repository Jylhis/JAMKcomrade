<!doctype html>
<html>
<head>
<meta charset="utf-8">
      <title>Ruoka</title>
      </head>
      <body>
<?php

      date_default_timezone_set('Europe/Helsinki');

if(isset($_GET['week']) && $_GET['week']>0 && $_GET['week']<=52) {
    $week = sprintf("%02d",$_GET['week']);
} else {
    $week = date('W');
}
if(isset($_GET['year'])) {
    $year = $_GET['year'];
} else {
    $year = date('Y');
}

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
if ($year!=date("Y")) {
    echo "Year must be current year";
    return;
}
if (ltrim($week,'0')<date("W") || ltrim($week,'0')>date("W")+1) {
    echo "Week must be between ".ltrim(date("W"),'0')."-".(date("W")+1);
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

function GetAimo($week, $year) {
    $date = new DateTime();
    $date = $date->setISODate($year, $week)->format('Y/m/d');

    $url = "http://www.amica.fi/modules/json/json/Index?costNumber=0350&language=fi&firstDay={$date}";
    $rawdata = file_get_contents($url);
    $json = json_decode($rawdata);

    $days = $json->MenusForDays;

    $odata = Array();

    $numDay = 0;
    foreach($days as $day) {
        $dayarr = Array();
        foreach($day->SetMenus as $foods) {
            //array_push($components, $comp);
            //print_r($foods);
            $foodarr= Array();

            $name = $foods->Name;
            $price = $foods->Price;

            $comps = Array();
            foreach($foods->Components as $comp) {
                array_push($comps, $comp);
            }

            array_push($foodarr, $name);
            array_push($foodarr, $price);
            array_push($foodarr, $comps);

            array_push($dayarr, $foodarr);
        }
        array_push($odata, $dayarr);
        ++$numDay;
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
                    echo "Ruoka: {$day[0]}<br>";
                    echo "Hinta: {$day[1]}<br>";
                    echo "Ruokainekset: "; // TODO: LOOP
                    foreach($day[2] as $comps) {
                        echo $comps;
                    }
                    echo"<br><br>";
                }
            }
            file_put_contents('cache/aimo-'.$week .'-'. $year, ob_get_contents());
            ob_end_clean();
    }
}