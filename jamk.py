#!/bin/python3

# The MIT License (MIT)
#
# Copyright (c) 2016 Markus Jylhänkangas
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

import requests
from bs4 import BeautifulSoup
from datetime import date
import re
import argparse

pvm= date.today().strftime("%y%m%d")

parser = argparse.ArgumentParser(description='Hakee JAMK lukujärjestyksen.')
parser.add_argument('ryhma', metavar='Ryhmätunnus', help='Ryhmän tunnus')
parser.add_argument('--json', action='store_true', help='Output in json')

args = parser.parse_args()
luokka = args.ryhma.upper()

url='https://amp.jamk.fi/asio/kalenterit2/index.php?av_v=1&av=160315160315160315&cluokka={0}&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm={1}&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1458049311&av_y=0&print=netti&outmode=excel_inline'.format(luokka,pvm)

r = requests.get(url)

data = [[],[],[],[],[]]
newData = {0: {},1: {},2: {},3: {},4: {}}
days=["Maanantai","Tiistai","Keskiviikko", "Torstai", "Perjantai"]

# Scrape data from https://amp.jamk.fi/asio/kalenterit2/index.php?guest=/asiakas12
if r.status_code == 200:
    html = r.text
    soup = BeautifulSoup(html, 'html.parser')
    rows = soup.table.tbody.find_all('tr')
    for tr in rows: 
        cols = tr.find_all('td')
        i = 0
        for td in cols:
            div = td.find('div')
            span = div.find_all('span')
            for tx in span:
                data[i].append(tx.text)
            i += 1

# Parse data with regex
prev=""
i = 0
for day in data:
    j=0
    for lesson in day:
        
        if lesson == prev:
            continue
        else:
            prev = lesson
            j+=1
            time = re.search("\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2}", lesson).group(0)
            courseID = re.search("[A-Z]{4}\d{4}", lesson).group(0)
            room = re.search("[0-9]?[A-Z][0-9]_[A-Z][0-9]{3}", lesson).group(0)
            name = re.sub("(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s([A-Z]{4}\d{4}\.(\d\w){2}\d)", '',lesson)
            name = re.sub("([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)",'', name).strip(' ')

            newData[i][j] = {"time":time,"room":room,"name":name,"courseid":courseID}
    i+=1

# Output
outputFormatted = ""
for day in newData:
    outputFormatted += "\n"+days[day] +"\n"
    for n in newData[day]:
        outputFormatted += newData[day][n]["name"]+", "+newData[day][n]["room"]+", "+newData[day][n]["time"]+"\n"

if args.json:
    print(newData)
else:
    print(outputFormatted)
