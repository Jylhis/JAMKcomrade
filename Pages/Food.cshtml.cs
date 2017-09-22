using Microsoft.AspNetCore.Mvc.RazorPages;
using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Net;

namespace JAMKcomrade.Pages
{
    public class Food
    {
        public string Name;
        public string Price;
        public List<string> Components;
    }

    public class Day
    {
        public string Name;
        public List<Food> FoodList;
    }

    public class AboutModel : PageModel
    {
        public string Message { get; set; }

        public List<Day> Week;

        public string RestaurantName;
        public int weekNum;
        public int Year;

        private string[] DayName = new string[]
        {
            "Maanantai",
            "Tiistai",
            "Keskiviikko",
            "Torstai",
            "Perjantai",
            "Lauantai",
            "Sunnuntai"
        };

        public void OnGet()
        {
            Message = "Your application description page.";

            // TODO: GET variables
            int Year = 0;//Request.QueryString["year"];
            if (Year == 0)
            {
                // current year number
                Year = DateTime.Now.Year;
            }

            // TODO: GET
            int weekNum = 0; //Request.QueryString["week"];
            if (weekNum == 0)
            {
                // current week number
                weekNum = GetIso8601WeekOfYear(DateTime.Now);
            }

            string searchDate = FirstDateOfWeekISO8601(Year, weekNum).ToString("yyyy/M/d");
            string url = "http://www.amica.fi/modules/json/json/Index?costNumber=0350&language=fi&firstDay=" + searchDate;


            using (WebClient wc = new WebClient())
            {
                dynamic json = JsonConvert.DeserializeObject(wc.DownloadString(url));

                //Week = WebCache.Get(restaurant + weekNum + year);
                Week = null;

                RestaurantName = json.RestaurantName;

                if (Week == null)
                {
                    Week = new List<Day>();
                    int WeekDayNum = 0;

                    foreach (var day in json.MenusForDays)
                    {
                        if (day != null)
                        {
                            Day today = new Day();

                            today.Name = DayName[WeekDayNum];
                            today.FoodList = new List<Food>();

                            foreach (var todaysFood in day.SetMenus)
                            {
                                List<string> components = new List<string>();

                                foreach (var component in todaysFood.Components)
                                {
                                    components.Add((string)component);
                                }

                                Food food = new Food
                                {
                                    Name = todaysFood.Name,
                                    Price = todaysFood.Price,
                                    Components = components
                                };


                                today.FoodList.Add(food);
                            }

                            Week.Add(today);
                            //week.append(todayArray);
                        }

                        if (WeekDayNum > 5)
                        {
                            break;
                        }
                        ++WeekDayNum;
                    }
                    //WebCache.Set(restaurant + weekNum + year, Week, 43829, false);

                }


            }
        }

        // This presumes that weeks start with Monday.
        // Week 1 is the 1st week of the year with a Thursday in it.
        // https://stackoverflow.com/questions/11154673/get-the-correct-week-number-of-a-given-date
        public static int GetIso8601WeekOfYear(DateTime time)
        {
            // Seriously cheat.  If its Monday, Tuesday or Wednesday, then it'll 
            // be the same week# as whatever Thursday, Friday or Saturday are,
            // and we always get those right
            DayOfWeek day = System.Globalization.CultureInfo.InvariantCulture.Calendar.GetDayOfWeek(time);
            if (day >= DayOfWeek.Monday && day <= DayOfWeek.Wednesday)
            {
                time = time.AddDays(3);
            }

            // Return the week of our adjusted day
            return System.Globalization.CultureInfo.InvariantCulture.Calendar.GetWeekOfYear(time, System.Globalization.CalendarWeekRule.FirstFourDayWeek, DayOfWeek.Monday);
        }

        // https://stackoverflow.com/questions/662379/calculate-date-from-week-number
        private static DateTime FirstDateOfWeekISO8601(int year, int weekOfYear)
        {
            DateTime jan1 = new DateTime(year, 1, 1);
            int daysOffset = DayOfWeek.Thursday - jan1.DayOfWeek;

            DateTime firstThursday = jan1.AddDays(daysOffset);
            var cal = System.Globalization.CultureInfo.CurrentCulture.Calendar;
            int firstWeek = cal.GetWeekOfYear(firstThursday, System.Globalization.CalendarWeekRule.FirstFourDayWeek, DayOfWeek.Monday);

            var weekNum = weekOfYear;
            if (firstWeek <= 1)
            {
                weekNum -= 1;
            }
            var result = firstThursday.AddDays(weekNum * 7);
            return result.AddDays(-3);
        }
    }
}
