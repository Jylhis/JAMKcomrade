using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc.RazorPages;
using HtmlAgilityPack;
using System.Text.RegularExpressions;

namespace JAMKcomrade.Pages
{
    public class Tunti
    {
        public string time;
        public string room;
        public string name;
        public string courseid;
    }
    public class Paiva
    {
        public List<Tunti> Tunnit = new List<Tunti>();
        public string Name;
    }
    public class TimetableModel : PageModel
    {
        private string[] DayName = new string[]
        {
            "Maanantai",
            "Tiistai",
            "Keskiviikko",
            "Torstai",
            "Perjantai"
        };

        public List<Paiva> Week = new List<Paiva>()
        {
            new Paiva(),
            new Paiva(),
            new Paiva(),
            new Paiva(),
            new Paiva()
        };
        // TODO: Korjaa ja siisti
        public void OnGet()
        {
            int year = 2017;
            int week = 39;
            string date = CustomDates.FirstDateOfWeekISO8601(year, week).ToString("yyMMdd");
            string luokka = "TTV15S3";

            string url = "https://amp.jamk.fi/asio_v16/kalenterit2/index.php?av_v=1&av=" + date + date + date + "&cluokka=" + luokka + "&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm=" + date + "&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1477646356&av_y=0&print=netti&outmode=excel_inline";

            var data = new List<List<string>>();
            var oData = new List<List<Tunti>>();
            // Lataa HTML
            var web = new HtmlWeb();
            var doc = web.Load(url);

            // Hae oikea data HTML:ästä
            var tbody = doc.DocumentNode.Descendants("tbody");
            var rows = tbody.First().Descendants("tr");

            foreach (var row in rows)
            {
                var cols = row.Descendants("td");
                for (int i = 0; i < cols.Count(); i++)
                {
                    var div = cols.ElementAt(i).Descendants("div");
                    var span = div.ElementAt(0).Descendants("span");

                    var Entry = new List<string>();
                    foreach (var txt in span)
                    {
                        Entry.Add(txt.InnerText);

                    }

                    if (Entry.Count > 0)
                    {
                        data.Add(Entry);
                    }
                }

            }

            // Erottele data
            string prev = "";
            for (int i = 0; i < data.Count; i++)
            {
                var day = new List<Tunti>();
                for (int j = 0; j < data[i].Count; j++)
                {
                    if (prev.Equals(data[i][j]))
                    {
                        continue;
                    }
                    else
                    {
                        prev = data[i][j];


                        // Tunnistaa ajan muodossa 00:00-00:00 ja 00-00
                        string timeP = @"\d{2}:\d{2}" // Ensimmäiset numerot. 00:00
                               + "-"                    // Ajan erottaja
                               + @"\d{2}:\d{2}"        // Sama kuin ensimmäinen aika. 00:00
                               + @"|\d{2}-\d{2}";     // Ajan toinen muoto ilman minuutteja 00-00

                        // Tunnistaa kurssi tunnukset
                        string courseP = @"([A-Z]{4}\d{4})"  // Muoto: AAAA0000
                                 + "|LUMA"                    // LUMA
                                 + @"|([A-Z]{5}\d{3})";      // Muoto: AAAAA000

                        // Luokka numero/tunnus
                        string roomP = "[A-Z]{1,2}[a-z]{0,1}"  // A Tai AA, 0-1 a
                               + "[0-9]{1,2}_{0,1}"             // 0 tai 00, 0-1 _
                               + "[A-Z]{1,2}-{0,1}"             // A Tai AA, 0-1 -
                               + "([0-9]{2,3}"                  // Osa 1: 00 Tai 000
                               + "|[a-z]{3,4})"                 // Tai aaa Tai aaaa :Osa 1
                               + "_{0,1}"                       // 0-1 _
                               + @"([a-z\d]{1,6})"              // Osa 2: a0 1-6 kerta : Osa 2
                               + "*"                            // Wildcard
                               + @"(\.\d|_\w*)"               // Osa 3: .0 TAI _sana : Osa 3
                               + "*";                          // Wildcard

                        string time = Regex.Match(data[i][j], timeP).Value;
                        string course = Regex.Match(data[i][j], courseP).Value;
                        //empty($course) ? $course = "" : $course = $course[0];

                        string room = Regex.Match(data[i][j], roomP).Value;
                        //empty($room) ? $room = "" : $room = $room[0];

                        // UUDET mutta ei toiminnassa
                        var name = Regex.Replace(data[i][j], @"(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s"
                                            + @"(([A-Z]{4,5}\d{3,4}\.\d\w(\d|\w)\w\d)|LUMA){0,1}\W*", string.Empty);
                        name = Regex.Replace(name, @"([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)", string.Empty);

                        // var nameT = Regex.Split(data[i][j], @"/(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s"
                        //         +@"(([A-Z]{5}\d{3}\.(\d\w){2}\d)|LUMA|([A-Z]{4}"
                        //        +@"\d{4}\.(\d\w){2}\d))\W*/");
                        //nameT = Regex.Split(nameT[1], @"/([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)/");

                        // if(empty($name[1])) print_r($name);
                        //string name = nameT[0];

                        //if (empty($name[0])) print_r($name);

                        Tunti tun = new Tunti
                        {
                            time = time,
                            room = room,
                            name = name,
                            courseid = course
                        };
                        
                        day.Add(tun);
                    }
                }

                oData.Add(day);

            }

            //output
            {
                var k = 0;
                for (int i = 0; i < DayName.Count(); i++)
                {
                    Week[i].Name = DayName[i];
                    foreach (var course in oData[k])
                    {
                        Week[i].Tunnit.Add(course);
                    }
                    k++;
                }
            }

        }

        /// Hakee JAMK:in ryhmät JAMK:in tilanvarausjärjestelmästä
        private void FetchGroups()
        {
            string url = "https://amp.jamk.fi/asio/kalenterit2/index.php?kt=lk&guest=%2Fasiakas12&lang=fin";

            var web = new HtmlWeb();
            var doc = web.Load(url);

            var select = doc.DocumentNode.Descendants("select");
            var options = select.ElementAt(0).Descendants("option");


            List<string> groups = new List<string>();

            foreach (var luokka in options)
            {
                if (Regex.Match(luokka.ToString(), "/Valitse ryhmä|"
                             + "^Aikuisryhmä$|"
                             + "^Hankintaosaaja$|"
                             + "^Industrial$|"
                             + "^IT$|"
                             + "^LYK$"
                             + "/") != null)
                {
                    continue;
                }
                groups.Add(luokka.ToString());
            }

            // TODO: Add to cache
            //apcu_add("groups",$groups, 21024000);
        }

    }
}
