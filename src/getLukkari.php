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

// Hakee päivämäärän perusteella lukujärjestyksiä JAMK:in tilanvaraus järjestelmästä
function Get($luokka, $week, $year) {

    $date = new \DateTime();
    $date = $date->setISODate($year, $week)->format('ymd');
    $luokka = strtoupper($luokka);

    $url = "https://amp.jamk.fi/asio_v16/kalenterit2/index.php?av_v=1&av={$date}{$date}{$date}&cluokka={$luokka}&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm={$date}&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1477646356&av_y=0&print=netti&outmode=excel_inline";

    $data = array(
        0 => array(), // Monday
        1 => array(), // Tuesday
        2 => array(), // Wednesday
        3 => array(), // Thursday
        4 => array()  // Friday
    );

    // Lataa HTML
    $html = file_get_contents($url);
    $doc = new \DOMDocument();
    $doc->loadHTML($html);

    // Hae oikea data HTML:ästä
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

    // Erottele data
    $prev = "";
    for($i = 0; $i < count($data); ++$i) {
        for($j = 0; $j < count($data[$i]); ++$j) {

            if(strcmp($prev, $data[$i][$j]) === 0) {
                continue;
            } else {
                $prev = $data[$i][$j];

                // Tunnistaa ajan muodossa 00:00-00:00 ja 00-00
                $timeP = '/\d{2}:\d{2}'      // Ensimmäiset numerot. 00:00
                       . '-'                 // Ajan erottaja
                       . '\d{2}:\d{2}'       // Sama kuin ensimmäinen aika. 00:00
                       . '|\d{2}-\d{2}/';    // Ajan toinen muoto ilman minuutteja 00-00

                // Tunnistaa kurssi tunnukset
                $courseP = "/([A-Z]{4}\d{4})"     // Muoto: AAAA0000
                         . "|LUMA"                // LUMA
                         . "|([A-Z]{5}\d{3})/";   // Muoto: AAAAA000

                // Luokka numero/tunnus
                $roomP = "/[A-Z]{1,2}[a-z]{0,1}"  // A Tai AA, 0-1 a
                       . "[0-9]{1,2}_{0,1}"       // 0 tai 00, 0-1 _
                       . "[A-Z]{1,2}-{0,1}"       // A Tai AA, 0-1 -
                       . "([0-9]{2,3}"            // Osa 1: 00 Tai 000
                       . "|[a-z]{3,4})"           // Tai aaa Tai aaaa :Osa 1
                       . "_{0,1}"                 // 0-1 _
                       . "([a-z\d]{1,6})"         // Osa 2: a0 1-6 kerta : Osa 2
                       . "*"                      // Wildcard
                       . "(\.\d|_\w*)"            // Osa 3: .0 TAI _sana : Osa 3
                       . "*/";                    // Wildcard

                preg_match($timeP, $data[$i][$j], $time);
                preg_match($courseP, $data[$i][$j], $course);
                empty($course) ? $course = "" : $course = $course[0];

                preg_match($roomP, $data[$i][$j], $room);
                empty($room) ? $room ="" : $room = $room[0];

                // FIXME: nimi regex. Käytä aikaisemmin määritettyjä regexejä
                $name = preg_split("/(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s"
                                  ."(([A-Z]{4,5}\d{3,4}\.\d\w(\d|\w)\w\d)|LUMA){0,1}\W*/",
                                   $data[$i][$j]);
                $name = preg_split("/([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)/", $name[1]);
                // if(empty($name[1])) print_r($name);
                $name = $name[0];

                if(empty($name[0])) print_r($name);

                $odata[$i][$j]["time"] = $time[0];
                $odata[$i][$j]["room"] = $room;
                $odata[$i][$j]["name"] = $name;
                $odata[$i][$j]["courseid"] = $course;

                /* TODO
                   $entryFormatted = array(
                   "Aika" => $time[0],
                   "Luokka" => $room,
                   "Kurssi" => $name,
                   "Tunnus" => $course
                   );
                 */
            }
        }
    }

    // Ulostulo TODO: Integroi tämä ylempään blokkiin
    if(empty($odata)) {
        apcu_add($luokka.'-'.$week.'-'.$year, false, 54000);
        return;
    } else {
        $weekday = array(
            "Maanantai" => array(),
            "Tiistai" => array(),
            "Keskiviikko" => array(),
            "Torstai" => array(),
            "Perjantai" => array()
        );

        $i=0;
        foreach($weekday as $key => $value) {
            foreach($odata[$i] as $course) {
                $courseData = array(
                    "Kurssi" => $course['name'],
                    "Tunnus" => $course['courseid'],
                    "Aika" => $course['time'],
                    "Luokka" => $course['room'],
                );
                array_push($weekday[$key], $courseData);
            }
            ++$i;
        }
        apcu_add($luokka.'-'.$week.'-'.$year, $weekday, 2628000); // 1 month
    }
}
