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

function GetAimo($week, $year) {
    $date = new \DateTime();
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

    if(empty($odata)) {
        apcu_add("Aimo-".$week.'-'.$year, false, 54000);
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
            foreach($odata[$i] as $day) {
                $courseData = array(
                    "Ruoka" => $day[0],
                    "Hinta" => $day[1],
                    "Ruokainekset" => array()
                );
                foreach($day[2] as $comps) {
                    array_push($courseData["Ruokainekset"], $comps);
                }
                array_push($weekday[$key], $courseData);
            }
            ++$i;
        }
        apcu_add("Aimo-".$week.'-'.$year, $weekday, 2628000); // 1 month

    }
}
