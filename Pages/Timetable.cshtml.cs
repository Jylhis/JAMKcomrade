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
        public List<Paiva> Week = new List<Paiva>()
        {
            new Paiva(){ Name="Maanantai"},
            new Paiva(){ Name="Tiistai"},
            new Paiva(){ Name="Keskiviikko"},
            new Paiva(){ Name="Torstai"},
            new Paiva(){ Name="Perjantai"}
        };

        public List<string> Groups = new List<string>();

        public void OnGet()
        {

            FetchGroups();

            int year = 2017;
            int week = 39;
            string date = CustomDates.FirstDateOfWeekISO8601(year, week).ToString("yyMMdd");
            string luokka = "TTV15S3";

            // Lataa HTML
            var data = new List<List<Tunti>>();

            string url = "https://amp.jamk.fi/asio_v16/kalenterit2/index.php?av_v=1&av=" + date + date + date + "&cluokka=" + luokka + "&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm=" + date + "&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1477646356&av_y=0&print=netti&outmode=excel_inline";
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
                    data.Add(new List<Tunti>());
                    var div = cols.ElementAt(i).Descendants("div");
                    var span = div.ElementAt(0).Descendants("span");
                    
                    // Ylensä vain yksi span, jos on enemmän tunnit on päällekkäin
                    foreach (var txt in span)
                    {
                        if (txt.InnerText != "")
                        {
                            data[i].Add(StringtoTunti(txt.InnerText));
                        }
                    }

                    
                }

            }


            //output
            {
                var k = 0;
                for (int i = 0; i < Week.Count(); i++)
                {
                    foreach (var course in data[k])
                    {
                        Week[i].Tunnit.Add(course);
                    }
                    k++;
                }
            }

            Week = CleanDuplicates(Week);

        }

        private List<Paiva> CleanDuplicates(List<Paiva> list)
        {
            // FIXME
            foreach (var day in list)
            {
                var tunnit = day.Tunnit;

                day.Tunnit = tunnit
                    .GroupBy(i => new { i.courseid, i.time})
                    .Select(g => g.First())
                    .ToList();
            }
            return list.Distinct().ToList();
        }

        private Tunti StringtoTunti(string str)
        {
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

            string time = Regex.Match(str, timeP).Value;
            string course = Regex.Match(str, courseP).Value;
            //empty($course) ? $course = "" : $course = $course[0];

            string room = Regex.Match(str, roomP).Value;
            //empty($room) ? $room = "" : $room = $room[0];

            // UUDET mutta ei toiminnassa
            var name = Regex.Replace(str, @"(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s"
                                + @"(([A-Z]{4,5}\d{3,4}\.\d\w(\d|\w)\w\d)|LUMA){0,1}\W*", string.Empty);
            name = Regex.Replace(name, @"([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)", string.Empty);

            return new Tunti
            {
                time = time,
                room = room,
                name = name,
                courseid = course
            };
        }

        /// Hakee JAMK:in ryhmät JAMK:in tilanvarausjärjestelmästä
        private void FetchGroups()
        {
            string url = "https://amp.jamk.fi/asio/kalenterit2/index.php?kt=lk&guest=%2Fasiakas12&lang=fin";

            var web = new HtmlWeb();
            var doc = web.Load(url);

            var select = doc.DocumentNode.Descendants("select");
            var options = select.ElementAt(0).Descendants("option");

            //List<string> Groups = new List<string>();

            foreach (var luokka in options)
            {
                if (Regex.Match(luokka.Attributes.First().Value, "/Valitse ryhmä|"
                             + @"^Aikuisryhmä$|^\s.$|"
                             + "^Hankintaosaaja$|"
                             + "^Industrial$|"
                             + "^IT$|"
                             + "^LYK$"
                             + "/").Success || luokka.Attributes.First().Value.Equals(""))
                {
                    continue;
                }
                Groups.Add(luokka.Attributes.First().Value);
            }

            // TODO: Add to cache
            //apcu_add("groups",$groups, 21024000);
        }

    }
}
