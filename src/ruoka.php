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
?>
<!doctype html>
<html lang="fi">
    <head>
        <meta charset="utf-8">
        <title>Ruoka</title>
        <meta name=viewport content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="favicon.png">
        <?php
        $WinterTheme = false;

        if(date('n')==12)
        {
            $WinterTheme = true;
        }
        if($WinterTheme)
        {
            echo "<link rel='stylesheet' href='winterstyle.css'>";
        } else {
            echo "<link rel='stylesheet' href='style.css'>";
        }
        echo"</style></head><body>";

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

        echo "<h1>Aimo ";

        if($week == 1) {
            echo "<a href='{$_SERVER['PHP_SELF']}?week=52&year={$lastyear}'><<</a>";
        } else {
            echo "<a href='{$_SERVER['PHP_SELF']}?week={$lastweek}&year={$year}'><<</a>";
        }

        echo "Week:{$week}";

        if ($week == 52) {
            echo "<a href='{$_SERVER['PHP_SELF']}?week=1&year={$nextyear}'>>></a>";

        } else {
            echo "<a href='{$_SERVER['PHP_SELF']}?week={$nextweek}&year={$year}'>>></a>";
        }

        echo " Year:".$year."</h1>";

        // Check date
        if ($year!=date("Y")) {
            echo "Year must be current year";
            return;
        }
        if (ltrim($week,'0')<date("W") || ltrim($week,'0')>date("W")+1) {
            echo "Week must be between ".ltrim(date("W"),'0')."-".(date("W")+1);
            return;
        }

        $cacheFile = "cache/aimo-" . $week .'-'.$year;
        if(!file_exists($cacheFile)) {
            echo GetAimo($week, $year);
        }
        echo file_get_contents($cacheFile);

        if($WinterTheme) {
            echo "<script>" . file_get_contents("snowstorm-min.js") . "</script>";
            echo "<script>snowStorm.followMouse = false;snowStorm.vMaxX = 3;snowStorm.vMaxY = 3;</script>";
        }
        ?>
</body>
</html>