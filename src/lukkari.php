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
require __DIR__ . '/getLukkari.php';
require __DIR__ . '/getRyhma.php';
require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Europe/Helsinki');
if(isset($_GET['week']) && $_GET['week']>0 && $_GET['week']<=52) {
    //$week = $_GET['week'];
    $week = sprintf("%02d",$_GET['week']);
} else {
    $week = date('W');
}
if(isset($_GET['year'])) {
    $year = $_GET['year'];
} else {
    $year = date('Y');
}

if(isset($_GET['luokka'])) {
    $luokka = strtoupper($_GET['luokka']);
    setcookie("group",$luokka,time()+94608000);
} else {
    if(isset($_COOKIE["group"])){
        $luokka = $_COOKIE["group"];
    } else {
        $luokka = "TTV15S3";
    }
}

$lastweek = $week-1;
$nextweek = $week+1;
$lastyear = $year-1;
$nextyear = $year+1;

if(!apcu_exists("groups"))
{
    FetchGroups();
}
?>

<!doctype html>
<html lang="fi">
    <head>
        <meta charset="utf-8"></head>
        <title>Lukujärjestys</title>
        <meta name="description" content="Helppp ja nopea sivu lukujärjestyksen katsomiseen.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/skeleton.css">

        <meta property="og:title" content="<?php{$luokka}?> - Lukujärjestys">
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://www.jylhis.com/jamk/lukkari.php?luokka=<?php{$luokka}?>&week=<?php{$week}?>&year=<?php{$year}?>">
        <link rel="icon" type="image/png" href="favicon.png">
    </head>
    <body>
        <div class="container">
            <div class="row header" align="center">
                <form method='get' action='lukkari.php'> <select name='luokka' onchange='this.form.submit()'>
                    <?php

                    // Group Select
                    foreach(apcu_fetch("groups") as $group) {
                        if(strcmp($group, $luokka)==0) {
                            echo "<option value='{$group}' selected='selected'>{$group}</option> ";
                        } else {
                            echo "<option value='{$group}'>{$group}</option> ";
                        }
                    }?>
                </select>
                </form>
                <div class='num'>

                    <?php
                    // Week select

                    // Left minus
                    if($week == 1) {
                        echo "<a rel='nofollow' href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week=52&year={$lastyear}'>&#10134</a>";
                    } else {
                        echo "<a rel='nofollow' href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week={$lastweek}&year={$year}'>&#10134;</a>";
                    }

                    echo "Viikko: {$week}";

                    // Right plus
                    if ($week == 52) {
                        echo "<a rel='nofollow' href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week=1&year={$nextyear}'>&#10133;</a>";

                    } else {
                        echo "<a rel='nofollow' href='{$_SERVER['PHP_SELF']}?luokka={$luokka}&week={$nextweek}&year={$year}'>&#10133;</a>";
                    }


                    // Year
                    echo "</div><div class='num'> Vuosi: {$year}</div></div>";

                    // Check date
                    if ($year<date("Y")-1 || $year>date("Y")+1) {
                        echo "Year must be between ".(date("Y")-1)."-".(date("Y")+1);
                        return;
                    }


                    print_r(Get($luokka, $week, $year));

                    // Load Content
                    $cache = $luokka . "-" . $week .'-'.$year;

                    if(!apcu_exists($cache."-HTML")) {
                        if(!apcu_exists($cache)) {
                            echo Get($luokka, $week, $year);
                        }
                        $datas = apcu_fetch($cache);

                        if($datas == false)
                        {
                            echo "<div class='row card'>No Data!</div>";
                        }
                        else {
                            ob_start();
                            foreach($datas as $key => $value) {
                                echo "<div class='row card'><h2>".$key."</h2>";
                                foreach($value as $data) {
                                    foreach($data as $key => $value) {
                                        echo $key.": ".$value."<br>";
                                        if(strcmp($key, "Luokka")==0) {
                                            echo "<br>";
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
