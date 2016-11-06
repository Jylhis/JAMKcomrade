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
<html lang="en">
<head>
<meta charset="utf-8">
<title>Lukkari</title>
<link rel="icon" type="image/png" href="favicon.png">
<style><?php echo preg_replace('/(\n)|(\s{4})/','',file_get_contents("style.css")) ?></style>
</head>
<body>
<a href="https://github.com/Jylhis/lukkari" class="github-corner" aria-label="View source on Github"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#fff; color:#a63636; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>
    <?php

        date_default_timezone_set('Europe/Helsinki');
        if(isset($_GET['week']) && $_GET['week']>0 && $_GET['week']<=52) {
            $week = $_GET['week'];
        } else {
            $week = date('W');
        }
        if(isset($_GET['year'])) {
            $year = $_GET['year'];
        } else {
            $year = date('Y');
        }
        if(isset($_GET['luokka'])) {
            $luokka = $_GET['luokka'];
        } else {
            $luokka = "TTV15S3";
        }

        $lastweek = $week-1;
        $nextweek = $week+1;
        $lastyear = $year-1;
        $nextyear = $year+1;

        echo "<h1>{$luokka} ";

        if($week == 1) {
            echo "<a href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week=52&year={$lastyear}'><<</a>";
        } else {
            echo "<a href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week={$lastweek}&year={$year}'><<</a>";
        }

        echo "Week: {$week}";

        if ($week == 52) {
            echo "<a href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week=1&year={$nextyear}'>>></a>";

        } else {
            echo "<a href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week={$nextweek}&year={$year}'>>></a>";
        }

        echo " Year: ".$year."</h1>";

        $cacheFile = "cache/" . $luokka . "-" . $week .'-'.$year;
        if(file_exists($cacheFile)) {
            echo file_get_contents($cacheFile);
        } else {
            echo Get($luokka, $week, $year);
            echo file_get_contents($cacheFile);
        }

        echo "<script>" . file_get_contents("snowstorm-min.js") . "</script>";
        ?>
<script>snowStorm.followMouse = false;snowStorm.vMaxX = 3;snowStorm.vMaxY = 3;</script>
</body>
</html>
<?php

function Get($luokka, $week, $year) {

    $date = new DateTime();
    $date = $date->setISODate($year, $week)->format('ymd');

    $url = "https://amp.jamk.fi/asio_v16/kalenterit2/index.php?av_v=1&av={$date}{$date}{$date}&cluokka={$luokka}&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm={$date}&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1477646356&av_y=0&print=netti&outmode=excel_inline";

    $data = array(
        0 => array(), // Monday
        1 => array(), // Tuesday
        2 => array(), // Wednesday
        3 => array(), // Thursday
        4 => array()  // Friday
    );

    // Load HTML
    $html = file_get_contents($url);
    $doc = new DOMDocument();
    $doc->loadHTML($html);

    // Scrape data from html
    $tbody = $doc->getElementsByTagName('tbody');
    $rows = $tbody->item(0)->getElementsByTagName('tr');
    foreach($rows as $row) {
        $cols = $row->getElementsByTagName('td');
        for($i = 0; $i < $cols->length; ++$i) {
            $div = $cols->item($i)->getElementsByTagName('div');
            $span = $div->item(0)->getElementsByTagName('span');
            foreach($span as $txt) {
                array_push($data[$i], $txt->nodeValue);
            }
        }
    }

    // Parse data with regex
    $prev = "";
    for($i = 0; $i < count($data); ++$i) {
        for($j = 0; $j < count($data[$i]); ++$j) {

            if(strcmp($prev, $data[$i][$j]) === 0) {
                continue;
            } else {
                $prev = $data[$i][$j];

                $timeP = '/\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2}/';
                $courseP = "/([A-Z]{4}\d{4})|LUMA|([A-Z]{5}\d{3})/";
                $roomP = "/[A-Z]{1,2}[a-z]{0,1}[0-9]{1,2}_{0,1}"
                        ."[A-Z]{1,2}-{0,1}([0-9]{2,3}|[a-z]{3,4})"
                        ."_{0,1}([a-z\d]{1,6})*(\.\d|_\w*)*/";

                preg_match($timeP, $data[$i][$j], $time);
                preg_match($courseP, $data[$i][$j], $course);
                preg_match($roomP, $data[$i][$j], $room);

                $name = preg_split("/(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s"
                                  ."(([A-Z]{5}\d{3}\.(\d\w){2}\d)|LUMA|([A-Z]{4}"
                                  ."\d{4}\.(\d\w){2}\d))\W*/", $data[$i][$j]);

                $name = preg_split("/([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)/", $name[1]);
                $name = $name[0];

                $odata[$i][$j]["time"] = $time[0];
                $odata[$i][$j]["room"] = $room[0];
                $odata[$i][$j]["name"] = $name;
                $odata[$i][$j]["courseid"] = $course[0];
            }
        }
    }

    // Output
    if(empty($odata)) {
        return;
    } else {
        // FIXME: Cache dir permission
        if (!file_exists('cache')) {
            mkdir('cache', 0744, true);
        }

        // Output json
        if(isset($_GET['json'])) {
            print_r(json_encode($odata));
        } else {
            // HTML
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
                    echo "kurssi: {$day['name']}<br>";
                    echo "Kurssi tunnus: {$day['courseid']}<br>";
                    echo "Aika: {$day['time']}<br>";
                    echo "Luokka: {$day['room']}<br>";
                    echo"<br>";
                }
            }
            file_put_contents('cache/'.$luokka.'-'.$week .'-'. $year, ob_get_contents());
            ob_end_clean();
        }
    }
}
?>
