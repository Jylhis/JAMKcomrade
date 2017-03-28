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

function Get($luokka, $week, $year) {

    $date = new \DateTime();
    $date = $date->setISODate($year, $week)->format('ymd');
    $luokka = strtoupper($luokka);

    $url = "https://amp.jamk.fi/asio_v16/kalenterit2/index.php?av_v=1&av={$date}{$date}{$date}&cluokka={$luokka}&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm={$date}&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1477646356&av_y=0&print=netti&outmode=excel_inline";

    $data = array();

    // Load HTML
    $html = file_get_contents($url);
    $doc = new \DOMDocument();
    $doc->loadHTML($html);

    // Scrape data from html
    $tbody = $doc->getElementsByTagName('tbody');
    $rows = $tbody->item(0)->getElementsByTagName('tr');
    foreach($rows as $row) {
        $cols = $row->getElementsByTagName('td');
        //for($i = 0; $i < $cols->length; ++$i) {

        // cols->items()??
        foreach($cols as $col) {
            $div = $col->getElementsByTagName('div');
            $span = $div->item(0)->getElementsByTagName('span');

            $columnarr = array();
            foreach($span as $txt) {
                array_push($columnarr, $txt->nodeValue);
            }
            array_push($data, $columnarr);
        }
    }

    // Parse data with regex
    $prev = "";
    $weekFormatted = array();
    foreach($data as $day) {
        $dayFormatted = array();
        foreach($day as $entry) {
            if(strcmp($prev, $entry) === 0) {
                continue;
            } else {

                $prev = $entry;

                $timeP = '/\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2}/';
                $courseP = "/([A-Z]{4}\d{4})|LUMA|([A-Z]{5}\d{3})/";
                $roomP = "/[A-Z]{1,2}[a-z]{0,1}[0-9]{1,2}_{0,1}"
                        ."[A-Z]{1,2}-{0,1}([0-9]{2,3}|[a-z]{3,4})"
                        ."_{0,1}([a-z\d]{1,6})*(\.\d|_\w*)*/";

                preg_match($timeP, $entry, $time);
                preg_match($courseP, $entry, $course);
                preg_match($roomP, $entry, $room);

                $name = preg_split("/(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s"
                                  ."(([A-Z]{4,5}\d{3,4}\.\d\w(\d|\w)\w\d)|LUMA){0,1}\W*/",
                                   $entry);

                $name = preg_split("/([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)/", $name[1]);
                // if(empty($name[1])) print_r($name);
                $name = $name[0];

                //if(empty($name[0])) print_r($name);
                $entryFormatted = array(
                    "Aika" => $time[0],
                    "Luokka" => $room[0],
                    "Kurssi" => $name,
                    "Tunnus" => $course[0]
                );
                array_push($dayFormatted, $entryFormatted);
            }
            array_push($weekFormatted, $dayFormatted);
        }
    }

    // Output
    if(empty($weekFormatted)) {
        apcu_add($luokka.'-'.$week.'-'.$year, false, 54000);
        return;
    } else {
        apcu_add($luokka.'-'.$week.'-'.$year, $weekFormatted, 2628000); // 1 month
    }
}
