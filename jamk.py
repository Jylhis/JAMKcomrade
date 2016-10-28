#!/usr/bin/env python3

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
import sys
sys.path.append("python-deps/requests-2.11.1/")
sys.path.append("python-deps/beautifulsoup4-4.5.1/")
from datetime import date
import re

import json
import requests
from bs4 import BeautifulSoup

today = date.today()
pvm = today.strftime("%y%m%d")
startPvm = today.replace(day=today.day - today.weekday()).strftime("%y%m%d")

luokka = sys.argv[1]

url='https://amp.jamk.fi/asio_v16/kalenterit2/index.php?av_v=1&av={1}{1}{1}&cluokka={0}&kt=lk&laji=%25%7C%7C%25&guest=%2Fasiakas12&lang=fin&ui=&yks=&apvm={2}&tiedot=kaikki&ss_ttkal=&ccv=&yhopt=&__cm=&b=1477646356&av_y=0&print=netti&outmode=excel_inline'.format(luokka,pvm,startPvm)

r = requests.get(url)
data = [[], [], [], [], []]
newData = {0: {}, 1: {}, 2: {}, 3: {}, 4: {}}

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
prev = ""
i = 0
for day in data:
    j = 0
    for lesson in day:

        if lesson == prev:
            continue
        else:
            prev = lesson
            j += 1

            time = re.search("\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2}", lesson).group(0)
            courseID = re.search("([A-Z]{4}\d{4})|LUMA|([A-Z]{5}\d{3})", lesson).group(0)

            # Ottaa mukaan myös luokan tyypin
            # room = re.search("([0-9]?[A-Z][0-9]_[A-Z][0-9]{3})\s([A-Z][a-z\s]+)", lesson).group(0)
            room = re.search("[0-9]?[A-Z][0-9]_[A-Z][0-9]{3}", lesson).group(0)

            # TODO: Tämän voi tehdä paremminki
            # Tämä etsii ajan ja tunnuksen rivin alusta ja poistaa ne
            name = re.sub("(\d{2}:\d{2}-\d{2}:\d{2}|\d{2}-\d{2})\s(([A-Z]{5}\d{3}\.(\d\w){2}\d)|LUMA|([A-Z]{4}\d{4}\.(\d\w){2}\d))", '', lesson)
            # Tämä etsii luokan ja poistaa kaiken sen jälkeen tulevan rivin lopusta
            name = re.sub("([0-9]?[A-z][0-9]_[A-Z][0-9]{3}).*\)", '', name).strip(' ')
            newData[i][j] = {"time":time, "room":room, "name":name, "courseid":courseID}
    i += 1

f = open("data.json", "w")
f.write(json.dumps(newData))
f.close()
#print(json.dumps(newData))
