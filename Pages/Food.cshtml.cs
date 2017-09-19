using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc.RazorPages;
using System.Web.Helpers;

namespace JAMKcomrade.Pages
{
    public class Food
    {
        public string Name;
        public string Price;
        public List<string> Components;
    }
    public class AboutModel : PageModel
    {
        public string Message { get; set; }
        public List<List<Food>> Week;
        public void OnGet()
        {
            Message = "Your application description page.";

            string year = "2017";
            int weekNum = 35;
            string restaurant = "Aimo";

            string searchDate = DateTime.Now.ToString("Y/m/d");
            string url = "http://www.amica.fi/modules/json/json/Index?costNumber=0350&language=fi&firstDay=" + searchDate;


            using (WebClient wc = new WebClient())
            {
                var json = wc.DownloadString(url);

                Week = WebCache.Get(restaurant + weekNum + year);

                if (Week == null)
                {
                    foreach (var day in json.MenusForDays)
                    {
                        if (day.isNotEmpty)
                        {
                            List<Food> today = new List<Food>();


                            foreach (var todaysFood in day.SetMenus)
                            {
                                List<string> components = new List<string>();

                                foreach (var component in todaysFood.Components)
                                {
                                    components.Add(component);
                                }

                                Food food = new Food
                                {
                                    Name = todaysFood.Name,
                                    Price = todaysFood.Price,
                                    Components = components
                                };


                                today.Add(food);
                            }

                            Week.Add(today);
                            //week.append(todayArray);
                        }
                    }
                    WebCache.Set(restaurant + weekNum + year, Week, 43829, false);
                    //AddToCache();
                    //apcu_add("Aimo-".$weekNum.'-'.$year, $week, 2628000); // 1 month

                }


            }
        }

    }
}
