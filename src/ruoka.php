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
namespace JAMKcomrade;
require __DIR__ . '/getAimo.php';
require __DIR__ . '/vendor/autoload.php';

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
?>
<!doctype html>
<html lang="fi">
    <head>
        <meta charset="utf-8">
        <title>Ruoka</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="favicon.png">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/skeleton.css">

        <meta property="og:title" content="Aimo Ruokalista">
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://www.jylhis.com/jamk/ruoka.php?&week=<?php{$week}?>&year=<?php{$year}?>">
    </head>
    <body>
        <div class="container">
            <div class="row header" align="center">
                <div class="num foodName">Aimo</div>
                <div class="num">
                    <?php
                    if($week == 1) {
                        echo "<a href='{$_SERVER['PHP_SELF']}?week=52&year={$lastyear}'>&#10134</a>";
                    } else {
                        echo "<a href='{$_SERVER['PHP_SELF']}?week={$lastweek}&year={$year}'>&#10134</a>";
                    }

                    echo "Viikko: {$week}";

                    if ($week == 52) {
                        echo "<a href='{$_SERVER['PHP_SELF']}?week=1&year={$nextyear}'>&#10133;</a>";

                    } else {
                        echo "<a href='{$_SERVER['PHP_SELF']}?week={$nextweek}&year={$year}'>&#10133;</a>";
                    }

                    echo "</div><div class='num'>Vuosi: {$year}</div>";

                    // Check date
                    if ($year!=date("Y")) {
                        echo "Year must be current year";
                        return;
                    }
                    // Check week
                    if (ltrim($week,'0')<date("W") || ltrim($week,'0')>date("W")+1) {
                        echo "Week must be between ".ltrim(date("W"),'0')."-".(date("W")+1);
                        return;
                    }
                    echo "</div>";

                    // Load content
                    $cache = "Aimo-" . $week .'-'.$year;


                    $weekdayName = array(
                        "Maanantai",
                        "Tiistai",
                        "Keskiviikko",
                        "Torstai",
                        "Perjantai"
                    );

                    if(!apcu_exists($cache."-HTML")) {
                        if(!apcu_exists($cache)) {
                            echo GetAimo($week, $year);
                        }
                        $datas = apcu_fetch($cache);

                        if($datas == false)
                        {
                            echo "<div class='row card'>No Data!</div>";
                        }
                        else {
                            ob_start();
                            foreach($datas as $key => $value) {
                                echo "<div class='row card'><h2>".$weekdayName[$key]."</h2>"; // Weekday
                                foreach($value as $data) {
                                    foreach($data as $key => $value) {

                                        if(strcmp($key, "Ruokainekset")==0) {
                                            //print_r($value);
                                            echo $key.":";
                                            foreach($value as $key => $value) {
                                                echo " ".$value;
                                            }
                                            echo "<br><br>";
                                        } else {
                                            echo $key.": ".$value."<br>";
                                        }

                                    }
                                }
                                echo "</div>";
                            }
                            apcu_add($cache."-HTML", ob_get_contents(), 2628000);
                            ob_end_clean();
                        }
                    }
                    echo apcu_fetch($cache."-HTML");
                    ?>
                </div>
    </body>
</html>
