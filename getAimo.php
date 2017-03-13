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
