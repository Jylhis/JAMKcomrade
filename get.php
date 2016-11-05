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

function Get($luokka, $week, $year = '2016') {
    date_default_timezone_set('Europe/Helsinki');
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
        exit();
    } else {
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
            file_put_contents('cache/'.$luokka.'-'.$week, ob_get_contents());
            ob_end_clean();
        }
    }
}
?>
