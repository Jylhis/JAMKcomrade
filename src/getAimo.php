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
    
    if(strcmp($days[0]->LunchTime,"null")==0) {
        apcu_add("Aimo-".$week.'-'.$year, false, 54000);
        return;
    } else {
        $week = array();
        foreach($days as $day) {
            $today = array();
            foreach($day->SetMenus as $todaysFood) {
                $foodInfo = array(
                    "Ruoka" => $todaysFood->Name,
                    "Hinta" => $todaysFood->Price,
                    "Ruokainekset" => array()
                );
                foreach($todaysFood->Components as $comps) {
                    array_push($courseData["Ruokainekset"], $comps);
                }
                array_push($today, $foodInfo);
            }
            array_push($week, $today);
        }
        apcu_add("Aimo-".$week.'-'.$year, $week, 2628000); // 1 month
    }