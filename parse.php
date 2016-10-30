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

/* Parametrit
   luokka=LUOKKATUNNUS
*/

// Parse cli arguments into $_GET
if (PHP_SAPI == 'cli') {
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

if(isset($_GET['luokka'])) {
    $luokka = $_GET['luokka'];
} else {
    $luokka = "TTV15S3";
}

date_default_timezone_set('Europe/Helsinki');
$date = date('ymd');
$fDate = date('ym') . date('d')-(date('N')-1);

$url = "https://amp.jamk.fi/asio_v16/kalenterit2/index.php?av_v=1&av={$date}{$date}{$date}&cluokka={$luokka}&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm={$fDate}&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1477646356&av_y=0&print=netti&outmode=excel_inline";

$data = array(
    0 => array(),
    1 => array(),
    2 => array(),
    3 => array(),
    4 => array()
);

// Load HTML
$html = file_get_contents($url);
$doc = new DOMDocument();
$doc->loadHTML($html);

// scrape data from html
$tbody = $doc->getElementsByTagName('tbody');
$rows = $tbody->item(0)->getElementsByTagName('tr');
foreach($rows as $row) {
    $cols = $row->getElementsByTagName('td');
    $i = 0;
    foreach($cols as $col){
        $div = $col->getElementsByTagName('div');
        $span = $div->item(0)->getElementsByTagName('span');
        foreach($span as $txt) {
            array_push($data[$i], $txt->nodeValue);
        }
        $i += 1;
    }
}

// Parse data with regex
$prev = "";
$i = 0;
$odata = array(
    0 => array(),
    1 => array(),
    2 => array(),
    3 => array(),
    4 => array()
);
foreach($data as $day) {
    $j = 0;
    foreach($day as $lesson) {
         if(strcmp($prev, $lesson) === 0) {
             continue;
        } else {
            $prev = $lesson;
            $j += 1;

            $timeP = '/\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2}/';
            $courseP = "/([A-Z]{4}\d{4})|LUMA|([A-Z]{5}\d{3})/";
            $roomP = "/[0-9]?[A-Z][0-9]_[A-Z][0-9]{3}/";

            preg_match($timeP, $lesson, $time);
            preg_match($courseP, $lesson, $course);
            preg_match($roomP, $lesson, $room);

            $name = preg_split("/(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s(([A-Z]{5}\d{3}\.(\d\w){2}\d)|LUMA|([A-Z]{4}\d{4}\.(\d\w){2}\d))\W*/", $lesson);
            $name = preg_split("/([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)/", $name[1]);
            $name = $name[0];

            $odata[$i][$j]["time"] = $time[0];
            $odata[$i][$j]["room"] = $room[0];
            $odata[$i][$j]["name"] = $name;
            $odata[$i][$j]["courseid"] = $course[0];
        }
    }
    $i += 1;
}

//file_put_contents('data.array', serialize($odata));
print_r(serialize($odata));
?>
