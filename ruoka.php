<?php
/* The MIT License (MIT)

   Copyright (c) 2016 Markus Jylhänkangas

   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software and associated documentation files (the "Software"), to deal
   in the Software without restriction, including without limitation the rights
   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   copies of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:

   The above copyright notice and this permission notice shall be included in all
   copies or substantial portions of the Software.

   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
   SOFTWARE.
*/
?>
<!doctype html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <title>Ruoka</title>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="favicon.png">
     <?php
    $WinterTheme = false;

if(date('n')==12)
{
    $WinterTheme = true;
}
if($WinterTheme)
{
    echo "<link rel='stylesheet' href='winterstyle.css'>";
} else {
     echo "<link rel='stylesheet' href='style.css'>";
}
echo"</style></head><body>";
if($WinterTheme) {
    echo '<a href="https://gitlab.com/Loylykauha/lukkari" class="github-corner" aria-label="View source on Github"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#fff; color:#a63636; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>';
} else {
    echo '<a href="https://gitlab.com/Loylykauha/lukkari" class="github-corner" aria-label="View source on Github"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>';
}

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

echo "Week:{$week}";

if ($week == 52) {
    echo "<a href='{$_SERVER['PHP_SELF']}?week=1&year={$nextyear}'>>></a>";

} else {
    echo "<a href='{$_SERVER['PHP_SELF']}?week={$nextweek}&year={$year}'>>></a>";
}

echo " Year:".$year."</h1>";

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
if($WinterTheme) {
    echo "<script>" . file_get_contents("snowstorm-min.js") . "</script>";
    echo "<script>snowStorm.followMouse = false;snowStorm.vMaxX = 3;snowStorm.vMaxY = 3;</script>";
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
                    echo "Ruokainekset: ";
                    foreach($day[2] as $comps) {
                        echo $comps." ";
                    }
                    echo"<br><br>";
                }
            }
            file_put_contents('cache/aimo-'.$week .'-'. $year, ob_get_contents());
            ob_end_clean();
    }
}
