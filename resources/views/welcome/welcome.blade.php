@extends('layouts.app')
@section('title', 'Kezdőlap')

@section('content')
<div class="container">
    <div class="row justify-content-between">
        <div class="col-12 col-md-12 welcome-box">
            @if(\App\Version::isNostrum())
                <h1>Tisztelt Collegista Társunk!</h1>

                <p>Az 1958-ban újjászervezett Eötvös Kollégium / Collegium idén ünnepli fennállásának 65. évfordulóját. Elhatároztuk, hogy erre az alkalomra – több korábbi kezdeményezésünket megújítva – létrehozunk egy, a honlapunkon elérhető adattárat, amely valamennyi egykori Eötvös-collegista közzétehető adatait tartalmazza (lehetőség szerint magába foglalja a teljes,1958–2010 közötti tagságot).</p>
                <p>Fazekas István Tanár úr (műhelyvezető) szakmai irányítása mellett a történész hallgatóinkból álló kutatócsoport nyáron megkezdte a munkát, és jól meghatározott minta alapján szerkeszti az egyes tagokra vonatkozó, nyilvánosan elérhető adatokat. Az adatsorok a történelmi Collegiumról készült 1946. évi kimutatás szellemében a collegisták későbbi pályájára vonatkozó összesítésekre is alkalmasak lesznek, hogy pontosabban dokumentálhassuk, milyen szerepet játszott / játszik a magyar tudományos és szellemi életben alma materünk.</p>
                <p>Boda Attila, a Collegium önkéntes levéltárosa kérésemre előkereste azt a minisztériumi beadványt, amit a Collegium főiskolai jellegének elnyerésére nyújtottak be 1946. április 19-én. (A Magyar Nemzeti Levéltár Országos Levéltárában őrzik.) A miniszter, Keresztury Dezső saját kezű írásával és aláírásával: Ilyen értelemben azonnal elintézendő (1946. június 18.) ügynek minősítette.</p>
                <p>Ebben a beadványban szerepel a történelmi Collegium eredményeinek összegzése:</p>
                <blockquote>„A Collegium főiskolai jellege működése eredményében is megfelelőképpen kifejezésre jutott. A Collegium 50 éves fennállása alatt a tanár- és tudósképzés területén kiváló eredményeket ért el. A Collegium e kiváló eredményeit legmegfelelőbben az alábbi adatok juttatják kifejezésre: A Collegiumban 50 éves fennállása alatt kb. 730 collégiumi tag fejezte be tanulmányait. Ezek közül egyetemi tanár, magántanár, főiskolai tanár lett 115; minisztériumi szolgálatban dolgozik kb. 25; tudományos intézetekben működik 60; külföldi szolgálatban 18; középfokú intézetekben 58 igazgató és kb. 400 tanár; önálló író és művész 20. A Magyar Tudományos Akadémia tagjai közül 44 a Collegium növendékei közül került ki.”</blockquote>
                <p><b>Az adattárat folyamatosan fejlesztjük és bővítjük. Köszönjük megértését a hiányosságokért és pontatlanságokért és köszönjük segítségét és támogatását!</b></p>
                <p>Tisztelettel, Horváth László, igazgató</p>

                <h2>Segítsen nekünk további információkkal!</h2>
                <p>Az adattárba folyamatosan töltjük fel azokat, akik a nyilvántartás szerint collegisták voltak. Az <em>Alumni adattár</em> menüpontra kattintva jelenik meg a kereshető adatbázis.</p>
                <p>Ha saját magához vagy ismerőséhez új adatokat szeretne feltölteni:</p>
                <ul>
                    <li>keressen rá név alapján a megfelelő bejegyzésre;</li>
                    <li>kattintson a <em>Részletek</em> gombra;</li>
                    <li>majd a jobb felső sarokban a <em>További információ beküldése</em> pontra.</li>
                </ul>
                <p>Igyekszünk minél gyorsabban egy teljességre törekvő adatbázist készíteni. Ha szeretné a munkánkat hiányzó emberekhez tartozó információkkal segíteni,
                    az ő adatait <a href="https://forms.gle/XocgGPS2WVQQXvyj8" target="_blank">az alábbi űrlapon</a> is feltöltheti.</p>
                <p><b>Előre is köszönjük!</b></p>
            @endif
            @if(\App\Version::isHellas())
                <h1>Tisztelt jelenlegi és korábbi újgörög szakos hallgatók!</h1>
                <p>Az 1975-ben alapított újgörög szak idén ünnepli fennállásának 50. évfordulóját. Elhatároztuk, hogy erre az alkalomra létrehozunk egy, a honlapunkon elérhető adattárat, amely valamennyi egykori újgörög szakos hallgató közzétehető adatait tartalmazza.</p>
                <p>Az adatbázis elsődleges célja a közösségépítés: egy összetartó és egymást szakmailag támogató újgörög szakos közösség létrehozása. Az adatsorok hallgatóink későbbi pályájára vonatkozó összesítésekre is alkalmasak lesznek, hogy pontosabban dokumentálhassuk, milyen szerepet játszott / játszik a magyar tudományos és szellemi életben alma materünk.</p>
                <p><strong>Az adattárat folyamatosan fejlesztjük és bővítjük. Köszönjük megértését a hiányosságokért és pontatlanságokért és köszönjük segítségét és támogatását!</strong></p>
                <p>Tisztelettel,<br/>
                az újgörög szak oktatói és munkatársai</p>
                <h2>Segítsen nekünk további információkkal!</h2>
                <p>Az adattárba folyamatosan töltjük fel azokat, akik újgörög szakosok voltak. Az Alumni adattár menüpontra kattintva jelenik meg a kereshető adatbázis.</p>
                <p>Ha saját magához vagy ismerőséhez új adatokat szeretne feltölteni:
                <ul>
                    <li>keressen rá név alapján a megfelelő bejegyzésre;</li>
                    <li>kattintson a <emph>Részletek</emph> gombra;</li>
                    <li>majd a jobb felső sarokban a <emph>További információ beküldése</emph> pontra.</li>
                </ul>
                </p>
                <p>Igyekszünk minél gyorsabban egy teljességre törekvő adatbázist készíteni. Ha szeretné a munkánkat hiányzó emberekhez tartozó információkkal segíteni, az ő adatait <a href="https://forms.gle/ZqrV7JRkMzcEiBLM9" target="_blank">az alábbi űrlapon</a> is feltöltheti.</p>
                <p><strong>Előre is köszönjük!</strong></p>
            @endif
        </div>
    </div>
</div>
@endsection
