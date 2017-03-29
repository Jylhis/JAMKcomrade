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

/// Hakee JAMK:in ryhmät JAMK:in tilanvarausjärjestelmästä
function FetchGroups() {
        $url = "https://amp.jamk.fi/asio/kalenterit2/index.php?kt=lk&guest=%2Fasiakas12&lang=fin";

        $html = file_get_contents($url);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);

        $select = $doc->getElementsByTagName('select');
        $options = $select->item(0)->getElementsByTagName('option');

        $groups = array();
        foreach($options as $luokka) {
            if(preg_match("/Valitse ryhmä|"
                         ."^Aikuisryhmä$|"
                         ."^Hankintaosaaja$|"
                         ."^Industrial$|"
                         ."^IT$|"
                         ."^LYK$"
                         ."/",$luokka->nodeValue)) {
                continue;
            }
            //        print_r($luokka->nodeValue);
            array_push($groups, $luokka->nodeValue);
        }
        apcu_add("groups",$groups, 21024000);
}
