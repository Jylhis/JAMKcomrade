# JAMKcomrade
Käytimme tätä kouluaikana lukujärjestyksen sekä ruokalistan tarkastamisessa.
Palvelin haki [tilanvarausohjelmistosta](https://amp.jamk.fi/asio) HTML sivun lukujärjestyksestä ja siitä parsetti haetun viikon
lukujärjestyksen. Ruokalistan palvelin hakee [Amican sivuilta](http://www.amica.fi/ravintolat/ravintolat-kaupungeittain/jyvaskyla/aimo/).

Dataan pääsee käsiksi selaimella sekä telegram botilla.

Lukujärjestys ei toimi joulukuu 2017 lähtien, koska tilanvarausjärjestelmä vaatii nykyään kirjautumisen.

# Branchit
Tässä vähän selityksiä brancheille. Branchit sisältävät keskeneräisiä töitä, mutta ajattelin laittaa tähän vähän tietoja jos 
niistä on joskus hyötyä.

#### Master
Viimeisin versio jota pyöritettiin palvelimella

#### Old
Luultavasti sama kuin `Master`, mutta ilman config tiedostoja ja php kirjastoja.

#### 4-yhdista-lukkari-php-ja-ruoka-php-koodia
Aloitin joskus PHP koodin siistimistä, mutta se jäi kesken. Tämä branch luultavasti sisältää pieniä parannuksia ja siivouksia.

#### dotnet
Uusin ja luultavasti parhain versio.
Melkein kokonaan C# portattu. 
Muistaakseni tästä puuttuu vain telegram botti käyttöliittymä.
